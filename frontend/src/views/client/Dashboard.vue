<template>
  <div class="container dashboard">
    <div class="dashboard-header">
      <h1>Catálogo de Filmes</h1>
      <p>Bem-vindo ao Preâmbulo Movies! Escolha seus filmes favoritos.</p>
    </div>

    <div v-if="loading" class="loading">Carregando filmes...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else class="movie-grid">
      <div v-for="movie in movies" :key="movie.id" class="movie-card">
        <img :src="movie.image_url" :alt="movie.title" class="movie-poster" />
        <div class="movie-info">
          <div class="movie-title">{{ movie.title }}</div>
          <div class="movie-meta">{{ movie.year }} • {{ movie.category }}</div>
          <div class="movie-meta">R$ {{ movie.rental_price }}</div>
          <div class="movie-meta">
            {{ movie.available_quantity > 0 ? `${movie.available_quantity} disponíveis` : 'Indisponível' }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'

const movies = ref([])
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    const response = await api.get('/movies')
    movies.value = response.data
  } catch (err) {
    error.value = 'Erro ao carregar filmes'
  } finally {
    loading.value = false
  }
})
</script>
