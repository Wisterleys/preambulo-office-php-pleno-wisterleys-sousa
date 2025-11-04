<template>
  <div class="admin-dashboard">
    <h1>Dashboard Administrativo</h1>
    
    <!-- KPI Cards -->
    <div class="kpi-grid">
      <div class="kpi-card">
        <div class="kpi-icon">📊</div>
        <div class="kpi-content">
          <h3>Locações Ativas</h3>
          <p class="kpi-value">{{ kpis.active_rentals || 0 }}</p>
        </div>
      </div>
      
      <div class="kpi-card">
        <div class="kpi-icon">💰</div>
        <div class="kpi-content">
          <h3>Faturamento do Mês</h3>
          <p class="kpi-value">R$ {{ formatMoney(kpis.month_revenue) }}</p>
        </div>
      </div>
      
      <div class="kpi-card">
        <div class="kpi-icon">📈</div>
        <div class="kpi-content">
          <h3>Taxa de Ocupação</h3>
          <p class="kpi-value">{{ kpis.occupation_rate || 0 }}%</p>
        </div>
      </div>
      
      <div class="kpi-card">
        <div class="kpi-icon">👥</div>
        <div class="kpi-content">
          <h3>Clientes Ativos</h3>
          <p class="kpi-value">{{ kpis.active_clients || 0 }}</p>
        </div>
      </div>
      
      <div class="kpi-card">
        <div class="kpi-icon">📅</div>
        <div class="kpi-content">
          <h3>Devoluções Hoje</h3>
          <p class="kpi-value">{{ kpis.today_returns || 0 }}</p>
        </div>
      </div>
      
      <div class="kpi-card">
        <div class="kpi-icon">⚠️</div>
        <div class="kpi-content">
          <h3>Multas Pendentes</h3>
          <p class="kpi-value">R$ {{ formatMoney(kpis.pending_fines) }}</p>
        </div>
      </div>
    </div>

    <!-- Acesso Rápido -->
    <div class="quick-access">
      <h2>Acesso Rápido</h2>
      <div class="quick-links">
        <router-link to="/admin/movies" class="quick-link">
          <span class="icon">🎬</span>
          <span>Gerenciar Filmes</span>
        </router-link>
        <router-link to="/attendant/clients" class="quick-link">
          <span class="icon">👥</span>
          <span>Gerenciar Clientes</span>
        </router-link>
        <router-link to="/attendant/rentals" class="quick-link">
          <span class="icon">📦</span>
          <span>Gerenciar Locações</span>
        </router-link>
        <router-link to="/admin/today-returns" class="quick-link">
          <span class="icon">📅</span>
          <span>Devoluções Hoje</span>
        </router-link>
        <router-link to="/admin/reports" class="quick-link">
          <span class="icon">📊</span>
          <span>Relatórios</span>
        </router-link>
      </div>
    </div>

    <!-- Gráficos -->
    <div class="charts-grid">
      <!-- Faturamento Mensal -->
      <div class="chart-card">
        <h3>Evolução do Faturamento</h3>
        <Line v-if="revenueChartData" :data="revenueChartData" :options="lineChartOptions" />
      </div>

      <!-- Composição da Receita -->
      <div class="chart-card">
        <h3>Composição da Receita</h3>
        <Doughnut v-if="revenueCompositionData" :data="revenueCompositionData" :options="doughnutChartOptions" />
      </div>

      <!-- Top 10 Filmes -->
      <div class="chart-card full-width">
        <h3>Top 10 Filmes Mais Alugados</h3>
        <Bar v-if="topMoviesData" :data="topMoviesData" :options="barChartOptions" />
      </div>

      <!-- Performance por Categoria -->
      <div class="chart-card">
        <h3>Locações por Categoria</h3>
        <Bar v-if="categoryData" :data="categoryData" :options="categoryChartOptions" />
      </div>

      <!-- Devoluções no Prazo vs Atrasadas -->
      <div class="chart-card">
        <h3>Análise de Devoluções</h3>
        <Pie v-if="returnAnalysisData" :data="returnAnalysisData" :options="pieChartOptions" />
      </div>

      <!-- Novos Clientes -->
      <div class="chart-card full-width">
        <h3>Novos Clientes por Mês</h3>
        <Line v-if="newClientsData" :data="newClientsData" :options="areaChartOptions" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Line, Bar, Doughnut, Pie } from 'vue-chartjs';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js';
import api from '../../services/api';

// Registrar componentes do Chart.js
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend,
  Filler
);

const kpis = ref({});
const revenueChartData = ref(null);
const revenueCompositionData = ref(null);
const topMoviesData = ref(null);
const categoryData = ref(null);
const returnAnalysisData = ref(null);
const newClientsData = ref(null);

// Opções dos gráficos
const lineChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      labels: { color: '#fff' }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: { color: '#fff' },
      grid: { color: 'rgba(255, 255, 255, 0.1)' }
    },
    x: {
      ticks: { color: '#fff' },
      grid: { color: 'rgba(255, 255, 255, 0.1)' }
    }
  }
};

const areaChartOptions = {
  ...lineChartOptions,
  elements: {
    line: {
      fill: true
    }
  }
};

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  indexAxis: 'y',
  plugins: {
    legend: {
      display: false
    }
  },
  scales: {
    y: {
      ticks: { color: '#fff' },
      grid: { color: 'rgba(255, 255, 255, 0.1)' }
    },
    x: {
      beginAtZero: true,
      ticks: { color: '#fff' },
      grid: { color: 'rgba(255, 255, 255, 0.1)' }
    }
  }
};

const categoryChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: { color: '#fff' },
      grid: { color: 'rgba(255, 255, 255, 0.1)' }
    },
    x: {
      ticks: { color: '#fff' },
      grid: { color: 'rgba(255, 255, 255, 0.1)' }
    }
  }
};

const doughnutChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
      labels: { color: '#fff' }
    }
  }
};

const pieChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
      labels: { color: '#fff' }
    }
  }
};

const formatMoney = (value) => {
  if (!value) return '0,00';
  return parseFloat(value).toFixed(2).replace('.', ',');
};

const loadKpis = async () => {
  try {
    const response = await api.get('/admin/dashboard/kpis');
    kpis.value = response.data;
  } catch (error) {
    console.error('Erro ao carregar KPIs:', error);
  }
};

const loadRevenueData = async () => {
  try {
    const response = await api.get('/admin/monthly-revenue');
    const evolution = response.data.monthly_evolution;
    
    revenueChartData.value = {
      labels: evolution.map(item => item.month),
      datasets: [
        {
          label: 'Locações',
          data: evolution.map(item => item.rentals),
          borderColor: '#00a8e1',
          backgroundColor: 'rgba(0, 168, 225, 0.1)',
          tension: 0.4
        },
        {
          label: 'Multas',
          data: evolution.map(item => item.fines),
          borderColor: '#ff6b6b',
          backgroundColor: 'rgba(255, 107, 107, 0.1)',
          tension: 0.4
        },
        {
          label: 'Total',
          data: evolution.map(item => item.total),
          borderColor: '#4ecdc4',
          backgroundColor: 'rgba(78, 205, 196, 0.1)',
          tension: 0.4
        }
      ]
    };

    // Composição da receita do mês atual
    const currentRevenue = response.data.revenue;
    revenueCompositionData.value = {
      labels: ['Locações', 'Multas'],
      datasets: [{
        data: [currentRevenue.rentals, currentRevenue.fines],
        backgroundColor: ['#00a8e1', '#ff6b6b'],
        borderWidth: 0
      }]
    };
  } catch (error) {
    console.error('Erro ao carregar dados de faturamento:', error);
  }
};

const loadTopMovies = async () => {
  try {
    const response = await api.get('/admin/dashboard/top-movies');
    const movies = response.data;
    
    topMoviesData.value = {
      labels: movies.map(m => m.title),
      datasets: [{
        label: 'Número de Locações',
        data: movies.map(m => m.rental_count),
        backgroundColor: '#00a8e1',
        borderWidth: 0
      }]
    };
  } catch (error) {
    console.error('Erro ao carregar top filmes:', error);
  }
};

const loadCategoryPerformance = async () => {
  try {
    const response = await api.get('/admin/dashboard/category-performance');
    const categories = response.data;
    
    categoryData.value = {
      labels: categories.map(c => c.category),
      datasets: [{
        label: 'Locações',
        data: categories.map(c => c.rental_count),
        backgroundColor: [
          '#00a8e1',
          '#4ecdc4',
          '#ff6b6b',
          '#ffd93d',
          '#6bcf7f',
          '#a29bfe',
          '#fd79a8',
          '#fdcb6e'
        ],
        borderWidth: 0
      }]
    };
  } catch (error) {
    console.error('Erro ao carregar performance por categoria:', error);
  }
};

const loadReturnAnalysis = async () => {
  try {
    const response = await api.get('/admin/dashboard/return-analysis');
    const data = response.data;
    
    returnAnalysisData.value = {
      labels: ['No Prazo', 'Atrasadas'],
      datasets: [{
        data: [data.on_time, data.late],
        backgroundColor: ['#4ecdc4', '#ff6b6b'],
        borderWidth: 0
      }]
    };
  } catch (error) {
    console.error('Erro ao carregar análise de devoluções:', error);
  }
};

const loadClientStatistics = async () => {
  try {
    const response = await api.get('/admin/dashboard/client-statistics');
    const evolution = response.data.monthly_evolution;
    
    newClientsData.value = {
      labels: evolution.map(item => item.month),
      datasets: [{
        label: 'Novos Clientes',
        data: evolution.map(item => item.count),
        borderColor: '#4ecdc4',
        backgroundColor: 'rgba(78, 205, 196, 0.3)',
        fill: true,
        tension: 0.4
      }]
    };
  } catch (error) {
    console.error('Erro ao carregar estatísticas de clientes:', error);
  }
};

onMounted(async () => {
  await loadKpis();
  await loadRevenueData();
  await loadTopMovies();
  await loadCategoryPerformance();
  await loadReturnAnalysis();
  await loadClientStatistics();
});
</script>

<style scoped>
.admin-dashboard {
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

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.kpi-card {
  background: linear-gradient(135deg, #1a242f 0%, #232f3e 100%);
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
  transition: transform 0.3s ease;
}

.kpi-card:hover {
  transform: translateY(-5px);
}

.kpi-icon {
  font-size: 2.5rem;
}

.kpi-content h3 {
  margin: 0;
  font-size: 0.9rem;
  color: #a0aec0;
  font-weight: 500;
}

.kpi-value {
  margin: 0.5rem 0 0 0;
  font-size: 1.8rem;
  font-weight: 700;
  color: #00a8e1;
}

.quick-access {
  margin-bottom: 3rem;
}

.quick-access h2 {
  color: #00a8e1;
  margin-bottom: 1rem;
  font-size: 1.5rem;
}

.quick-links {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.quick-link {
  background: #1a242f;
  color: #fff;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  border: 1px solid transparent;
}

.quick-link:hover {
  background: #232f3e;
  border-color: #00a8e1;
  transform: translateY(-2px);
}

.quick-link .icon {
  font-size: 1.5rem;
}

.charts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
  gap: 2rem;
}

.chart-card {
  background: #1a242f;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
  min-height: 350px;
}

.chart-card.full-width {
  grid-column: 1 / -1;
}

.chart-card h3 {
  margin: 0 0 1.5rem 0;
  color: #00a8e1;
  font-size: 1.1rem;
}

@media (max-width: 768px) {
  .kpi-grid {
    grid-template-columns: 1fr;
  }
  
  .charts-grid {
    grid-template-columns: 1fr;
  }
  
  .chart-card.full-width {
    grid-column: 1;
  }
  
  .quick-links {
    flex-direction: column;
  }
}
</style>
