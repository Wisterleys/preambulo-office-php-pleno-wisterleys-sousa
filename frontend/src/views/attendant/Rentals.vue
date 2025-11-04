<template>
  <div class="container dashboard">
    <div class="dashboard-header">
      <h1>Gerenciar Locações</h1>
    </div>
    <div v-if="loading" class="loading">Carregando...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else>
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Filmes</th>
            <th>Data Início</th>
            <th>Data Devolução</th>
            <th>Status</th>
            <th>Valor</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="rental in rentals" :key="rental.id">
            <td>#{{ rental.id }}</td>
            <td>{{ rental.client.name }}</td>
            <td>{{ rental.items.map(i => i.movie.title).join(', ') }}</td>
            <td>{{ formatDate(rental.start_date) }}</td>
            <td>{{ formatDate(rental.expected_return_date) }}</td>
            <td>
              <span :class="['badge', `badge-${rental.status}`]">
                {{ translateStatus(rental.status) }}
              </span>
            </td>
            <td>R$ {{ rental.total_amount }}</td>
            <td>
              <button v-if="rental.status === 'active' || rental.status === 'overdue'" 
                      @click="returnRental(rental.id)" 
                      class="btn btn-secondary">
                Devolver
              </button>
            </td>
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

const formatDate = (date) => new Date(date).toLocaleDateString('pt-BR')

const translateStatus = (status) => {
  const statuses = { active: 'Ativo', returned: 'Devolvido', overdue: 'Atrasado' }
  return statuses[status] || status
}

const returnRental = async (id) => {
  if (!confirm('Confirmar devolução?')) return
  try {
    await api.post(`/rentals/${id}/return`)
    const response = await api.get('/rentals')
    rentals.value = response.data
  } catch (err) {
    alert('Erro ao devolver locação')
  }
}

onMounted(async () => {
  try {
    const response = await api.get('/rentals')
    rentals.value = response.data
  } catch (err) {
    error.value = 'Erro ao carregar locações'
  } finally {
    loading.value = false
  }
})
</script>
