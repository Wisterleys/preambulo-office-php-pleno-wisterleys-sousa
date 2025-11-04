<template>
  <div class="catalogo">
    <div class="container">
      <h1>Catálogo de Filmes</h1>
      
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
      </div>

      <div v-else-if="error" class="alert alert-error">
        {{ error }}
      </div>

      <div v-else class="movies-grid">
        <div v-for="filme in filmes" :key="filme.uuid" class="movie-card">
          <img :src="filme.imagem_path || '/placeholder-movie.jpg'" :alt="filme.titulo" />
          <div class="movie-info">
            <div class="movie-title">{{ filme.titulo }}</div>
            <div class="movie-year">{{ filme.ano }} • {{ filme.categoria }}</div>
            <div class="movie-price">R$ {{ filme.valor_locacao }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const filmes = ref([])
const loading = ref(true)
const error = ref(null)

const fetchFilmes = async () => {
  try {
    const response = await axios.get('/api/filmes')
    filmes.value = response.data.data || []
  } catch (err) {
    error.value = 'Erro ao carregar filmes'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchFilmes()
})
</script>

<style scoped>
.catalogo {
  padding: 100px 20px 40px;
  min-height: 100vh;
}

h1 {
  margin-bottom: 40px;
}

.movie-price {
  color: var(--success-color);
  font-weight: 600;
  margin-top: 5px;
}
</style>
