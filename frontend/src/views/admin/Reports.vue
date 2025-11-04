<template>
  <div class="reports-page">
    <h1>Relatórios Gerenciais</h1>

    <!-- Seletor de Relatório -->
    <div class="report-selector">
      <button 
        v-for="report in reports" 
        :key="report.id"
        :class="['report-btn', { active: selectedReport === report.id }]"
        @click="selectReport(report.id)"
      >
        <span class="icon">{{ report.icon }}</span>
        <span>{{ report.name }}</span>
      </button>
    </div>

    <!-- Relatório de Devoluções Atrasadas -->
    <div v-if="selectedReport === 'overdue'" class="report-content">
      <h2>Clientes com Devoluções Atrasadas</h2>
      <div v-if="overdueRentals.length === 0" class="empty-state">
        <p>✅ Não há devoluções atrasadas no momento</p>
      </div>
      <table v-else class="report-table">
        <thead>
          <tr>
            <th>Cliente</th>
            <th>Filmes</th>
            <th>Data Prevista</th>
            <th>Dias de Atraso</th>
            <th>Multa</th>
            <th>Total Devido</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="rental in overdueRentals" :key="rental.id">
            <td>
              <strong>{{ rental.client.name }}</strong><br>
              <small>{{ rental.client.email }}</small>
            </td>
            <td>
              <div v-for="item in rental.items" :key="item.id" class="movie-item">
                {{ item.movie.title }}
              </div>
            </td>
            <td>{{ formatDate(rental.expected_return_date) }}</td>
            <td class="text-danger">{{ rental.days_overdue }} dias</td>
            <td class="text-danger">R$ {{ formatMoney(rental.calculated_fine) }}</td>
            <td class="text-warning">R$ {{ formatMoney(rental.total_amount + rental.calculated_fine) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Relatório de Faturamento Mensal -->
    <div v-if="selectedReport === 'revenue'" class="report-content">
      <h2>Faturamento Mensal</h2>
      
      <div class="filters">
        <select v-model="revenueMonth" @change="loadMonthlyRevenue">
          <option v-for="m in 12" :key="m" :value="m">{{ getMonthName(m) }}</option>
        </select>
        <select v-model="revenueYear" @change="loadMonthlyRevenue">
          <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
        </select>
      </div>

      <div class="revenue-summary">
        <div class="summary-card">
          <h3>Locações</h3>
          <p class="value">R$ {{ formatMoney(monthlyRevenue.revenue?.rentals) }}</p>
        </div>
        <div class="summary-card">
          <h3>Multas</h3>
          <p class="value">R$ {{ formatMoney(monthlyRevenue.revenue?.fines) }}</p>
        </div>
        <div class="summary-card highlight">
          <h3>Total</h3>
          <p class="value">R$ {{ formatMoney(monthlyRevenue.revenue?.total) }}</p>
        </div>
      </div>

      <div class="revenue-stats">
        <div class="stat-item">
          <span class="label">Total de Locações:</span>
          <span class="value">{{ monthlyRevenue.statistics?.total_rentals || 0 }}</span>
        </div>
        <div class="stat-item">
          <span class="label">Locações Ativas:</span>
          <span class="value">{{ monthlyRevenue.statistics?.active_rentals || 0 }}</span>
        </div>
        <div class="stat-item">
          <span class="label">Locações Atrasadas:</span>
          <span class="value">{{ monthlyRevenue.statistics?.overdue_rentals || 0 }}</span>
        </div>
        <div class="stat-item">
          <span class="label">Ticket Médio:</span>
          <span class="value">R$ {{ formatMoney(monthlyRevenue.statistics?.average_per_rental) }}</span>
        </div>
      </div>
    </div>

    <!-- Relatório de Valores Devidos -->
    <div v-if="selectedReport === 'debts'" class="report-content">
      <h2>Valores Devidos por Cliente</h2>
      <div v-if="clientDebts.length === 0" class="empty-state">
        <p>✅ Não há valores pendentes no momento</p>
      </div>
      <table v-else class="report-table">
        <thead>
          <tr>
            <th>Cliente</th>
            <th>Email</th>
            <th>Locações Ativas</th>
            <th>Valor das Locações</th>
            <th>Multas</th>
            <th>Total Devido</th>
            <th>Dias de Atraso</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="client in clientDebts" :key="client.client_id">
            <td><strong>{{ client.client_name }}</strong></td>
            <td>{{ client.client_email }}</td>
            <td>{{ client.active_rentals_count }}</td>
            <td>R$ {{ formatMoney(client.total_rental_amount) }}</td>
            <td class="text-danger">R$ {{ formatMoney(client.total_fines) }}</td>
            <td class="text-warning"><strong>R$ {{ formatMoney(client.total_owed) }}</strong></td>
            <td class="text-danger">{{ client.max_days_overdue }}</td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3"><strong>TOTAL</strong></td>
            <td><strong>R$ {{ formatMoney(totalRentalAmount) }}</strong></td>
            <td><strong>R$ {{ formatMoney(totalFines) }}</strong></td>
            <td><strong>R$ {{ formatMoney(totalOwed) }}</strong></td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Relatório de Top Filmes -->
    <div v-if="selectedReport === 'top-movies'" class="report-content">
      <h2>Filmes Mais Alugados</h2>
      
      <div class="filters">
        <select v-model="moviesPeriod" @change="loadTopMovies">
          <option value="all">Todos os tempos</option>
          <option value="month">Este mês</option>
          <option value="year">Este ano</option>
        </select>
      </div>

      <table class="report-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Filme</th>
            <th>Categoria</th>
            <th>Locações</th>
            <th>Receita</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(movie, index) in topMovies" :key="movie.movie_id">
            <td><strong>{{ index + 1 }}º</strong></td>
            <td><strong>{{ movie.title }}</strong></td>
            <td>{{ movie.category }}</td>
            <td>{{ movie.rental_count }}</td>
            <td>R$ {{ formatMoney(movie.revenue) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../services/api';

const reports = [
  { id: 'overdue', name: 'Devoluções Atrasadas', icon: '⚠️' },
  { id: 'revenue', name: 'Faturamento Mensal', icon: '💰' },
  { id: 'debts', name: 'Valores Devidos', icon: '💳' },
  { id: 'top-movies', name: 'Top Filmes', icon: '🏆' }
];

const selectedReport = ref('overdue');
const overdueRentals = ref([]);
const monthlyRevenue = ref({});
const clientDebts = ref([]);
const topMovies = ref([]);

const revenueMonth = ref(new Date().getMonth() + 1);
const revenueYear = ref(new Date().getFullYear());
const moviesPeriod = ref('all');

const years = computed(() => {
  const currentYear = new Date().getFullYear();
  return Array.from({ length: 5 }, (_, i) => currentYear - i);
});

const totalRentalAmount = computed(() => {
  return clientDebts.value.reduce((sum, client) => sum + client.total_rental_amount, 0);
});

const totalFines = computed(() => {
  return clientDebts.value.reduce((sum, client) => sum + client.total_fines, 0);
});

const totalOwed = computed(() => {
  return clientDebts.value.reduce((sum, client) => sum + client.total_owed, 0);
});

const selectReport = (reportId) => {
  selectedReport.value = reportId;
  loadReportData(reportId);
};

const loadReportData = async (reportId) => {
  switch (reportId) {
    case 'overdue':
      await loadOverdueRentals();
      break;
    case 'revenue':
      await loadMonthlyRevenue();
      break;
    case 'debts':
      await loadClientDebts();
      break;
    case 'top-movies':
      await loadTopMovies();
      break;
  }
};

const loadOverdueRentals = async () => {
  try {
    const response = await api.get('/admin/overdue-rentals');
    overdueRentals.value = response.data;
  } catch (error) {
    console.error('Erro ao carregar devoluções atrasadas:', error);
  }
};

const loadMonthlyRevenue = async () => {
  try {
    const response = await api.get('/admin/monthly-revenue', {
      params: {
        year: revenueYear.value,
        month: revenueMonth.value
      }
    });
    monthlyRevenue.value = response.data;
  } catch (error) {
    console.error('Erro ao carregar faturamento mensal:', error);
  }
};

const loadClientDebts = async () => {
  try {
    const response = await api.get('/admin/client-debts');
    clientDebts.value = response.data;
  } catch (error) {
    console.error('Erro ao carregar valores devidos:', error);
  }
};

const loadTopMovies = async () => {
  try {
    const response = await api.get('/admin/dashboard/top-movies', {
      params: {
        period: moviesPeriod.value
      }
    });
    topMovies.value = response.data;
  } catch (error) {
    console.error('Erro ao carregar top filmes:', error);
  }
};

const formatMoney = (value) => {
  if (!value) return '0,00';
  return parseFloat(value).toFixed(2).replace('.', ',');
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('pt-BR');
};

const getMonthName = (month) => {
  const months = [
    'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
  ];
  return months[month - 1];
};

onMounted(() => {
  loadOverdueRentals();
});
</script>

<style scoped>
.reports-page {
  padding: 2rem;
  color: #fff;
  max-width: 1400px;
  margin: 0 auto;
}

h1 {
  margin-bottom: 2rem;
  color: #00a8e1;
  font-size: 2rem;
}

.report-selector {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.report-btn {
  background: #1a242f;
  color: #fff;
  border: 2px solid transparent;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  font-size: 1rem;
}

.report-btn:hover {
  background: #232f3e;
  border-color: #00a8e1;
}

.report-btn.active {
  background: #00a8e1;
  border-color: #00a8e1;
}

.report-btn .icon {
  font-size: 1.5rem;
}

.report-content {
  background: #1a242f;
  border-radius: 12px;
  padding: 2rem;
}

.report-content h2 {
  color: #00a8e1;
  margin-bottom: 1.5rem;
}

.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.filters select {
  background: #0f171e;
  color: #fff;
  border: 1px solid #2d3748;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 1rem;
}

.report-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}

.report-table th,
.report-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #2d3748;
}

.report-table th {
  background: #0f171e;
  color: #00a8e1;
  font-weight: 600;
}

.report-table tbody tr:hover {
  background: #232f3e;
}

.report-table tfoot {
  background: #0f171e;
  font-weight: bold;
}

.movie-item {
  padding: 0.25rem 0;
  font-size: 0.9rem;
}

.text-danger {
  color: #ff6b6b;
}

.text-warning {
  color: #ffd93d;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #a0aec0;
  font-size: 1.1rem;
}

.revenue-summary {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.summary-card {
  background: #0f171e;
  padding: 1.5rem;
  border-radius: 8px;
  text-align: center;
}

.summary-card.highlight {
  background: linear-gradient(135deg, #00a8e1 0%, #0080b3 100%);
}

.summary-card h3 {
  margin: 0 0 0.5rem 0;
  color: #a0aec0;
  font-size: 0.9rem;
  font-weight: 500;
}

.summary-card .value {
  margin: 0;
  font-size: 2rem;
  font-weight: 700;
  color: #fff;
}

.revenue-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
  padding: 1.5rem;
  background: #0f171e;
  border-radius: 8px;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0;
}

.stat-item .label {
  color: #a0aec0;
}

.stat-item .value {
  color: #00a8e1;
  font-weight: 600;
}

@media (max-width: 768px) {
  .report-table {
    font-size: 0.9rem;
  }
  
  .report-table th,
  .report-table td {
    padding: 0.5rem;
  }
  
  .revenue-summary {
    grid-template-columns: 1fr;
  }
}
</style>
