<template>
  <div class="container dashboard">
    <div class="dashboard-header">
      <h1>Devoluções Previstas para Hoje</h1>
      <p>Funcionalidade exclusiva do Administrador</p>
    </div>
    <div v-if="loading" class="loading">Carregando...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else-if="rentals.length === 0" class="loading">Nenhuma devolução prevista para hoje.</div>
    <div v-else>
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>E-mail</th>
            <th>Telefone</th>
            <th>Filmes</th>
            <th>Status</th>
            <th>Valor</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="rental in rentals" :key="rental.id">
            <td>#{{ rental.id }}</td>
            <td>{{ rental.client.name }}</td>
            <td>{{ rental.client.email }}</td>
            <td>{{ rental.client.phone }}</td>
            <td>
              <div v-for="item in rental.items" :key="item.id">
                {{ item.movie.title }}
              </div>
            </td>
            <td>
              <span :class="['badge', `badge-${rental.status}`]">
                {{ translateStatus(rental.status) }}
              </span>
            </td>
            <td>R$ {{ rental.total_amount }}</td>
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

const translateStatus = (status) => {
  const statuses = { active: 'Ativo', returned: 'Devolvido', overdue: 'Atrasado' }
  return statuses[status] || status
}

onMounted(async () => {
  try {
    const response = await api.get('/admin/today-returns')
    rentals.value = response.data
  } catch (err) {
    error.value = 'Erro ao carregar devoluções'
  } finally {
    loading.value = false
  }
})
</script>
