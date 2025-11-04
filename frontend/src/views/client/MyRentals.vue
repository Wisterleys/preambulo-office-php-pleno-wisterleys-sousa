<template>
  <div class="container dashboard">
    <div class="dashboard-header">
      <h1>Minhas Locações</h1>
    </div>

    <div v-if="loading" class="loading">Carregando locações...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else-if="rentals.length === 0" class="loading">Você ainda não possui locações.</div>
    <div v-else>
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Filmes</th>
            <th>Data Início</th>
            <th>Data Devolução</th>
            <th>Status</th>
            <th>Valor Total</th>
            <th>Multa</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="rental in rentals" :key="rental.id">
            <td>#{{ rental.id }}</td>
            <td>
              <div v-for="item in rental.items" :key="item.id">
                {{ item.movie.title }}
              </div>
            </td>
            <td>{{ formatDate(rental.start_date) }}</td>
            <td>{{ formatDate(rental.expected_return_date) }}</td>
            <td>
              <span :class="['badge', `badge-${rental.status}`]">
                {{ translateStatus(rental.status) }}
              </span>
            </td>
            <td>R$ {{ rental.total_amount }}</td>
            <td>R$ {{ rental.fine_amount }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'

const rentals = ref([])
const loading = ref(true)
const error = ref('')

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

const translateStatus = (status) => {
  const statuses = {
    active: 'Ativo',
    returned: 'Devolvido',
    overdue: 'Atrasado'
  }
  return statuses[status] || status
}

onMounted(async () => {
  try {
    const response = await api.get('/my-rentals')
    rentals.value = response.data
  } catch (err) {
    error.value = 'Erro ao carregar locações'
  } finally {
    loading.value = false
  }
})
</script>
