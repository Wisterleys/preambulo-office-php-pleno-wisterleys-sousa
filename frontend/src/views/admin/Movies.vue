<template>
  <div class="container dashboard">
    <div class="dashboard-header">
      <h1>Gerenciar Filmes</h1>
    </div>
    <div v-if="loading" class="loading">Carregando...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else>
      <div class="action-bar">
        <button class="btn-primary" @click="createMovie">Novo Filme</button>
      </div>
      
      <div v-if="showForm" class="form-container">
        <h2>{{ formTitle }}</h2>
        <MovieForm 
          :movie="currentMovie" 
          :is-edit="isEdit" 
          @submit="submitForm" 
          @cancel="cancelForm" 
        />
      </div>

      <div v-else>
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Ano</th>
            <th>Categoria</th>
            <th>Preço</th>
            <th>Disponíveis</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="movie in movies" :key="movie.id">
            <td>{{ movie.id }}</td>
            <td>{{ movie.title }}</td>
            <td>{{ movie.year }}</td>
            <td>{{ movie.category }}</td>
            <td>R$ {{ movie.rental_price }}</td>
            <td>{{ movie.available_quantity }}</td>
            <td>
              <button class="btn-sm btn-warning" @click="editMovie(movie)">Editar</button>
              <button class="btn-sm btn-danger" @click="confirmDelete(movie)">Excluir</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    </div>

    <ConfirmModal
      :is-visible="showModal"
      title="Confirmar Exclusão"
      :message="`Tem certeza que deseja excluir o filme ${movieToDelete?.title}? Esta ação é irreversível.`"
      confirm-text="Sim, eu desejo remover"
      @confirm="deleteMovie"
      @close="showModal = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import MovieForm from '../../components/MovieForm.vue'
import ConfirmModal from '../../components/ConfirmModal.vue'
import api from '../../services/api'

const movies = ref([])
const loading = ref(true)
const error = ref('')
const showForm = ref(false)
const isEdit = ref(false)
const currentMovie = ref(null)
const showModal = ref(false)
const movieToDelete = ref(null)

const formTitle = computed(() => isEdit.value ? 'Editar Filme' : 'Novo Filme')

const fetchMovies = async () => {
  try {
    const response = await api.get('/movies')
    movies.value = response.data
  } catch (err) {
    error.value = 'Erro ao carregar filmes'
  } finally {
    loading.value = false
  }
}

onMounted(fetchMovies)

const createMovie = () => {
  isEdit.value = false
  currentMovie.value = {
    title: '',
    synopsis: '',
    year: new Date().getFullYear(),
    category: '',
    rental_price: 0.0,
    available_quantity: 0,
    image_url: null,
    video_url: null,
  }
  showForm.value = true
}

const editMovie = (movie) => {
  isEdit.value = true
  currentMovie.value = { ...movie }
  showForm.value = true
}

const cancelForm = () => {
  showForm.value = false
  currentMovie.value = null
}

const submitForm = async (formData) => {
  try {
    const config = {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    }
    
    if (isEdit.value) {
      // Laravel espera POST para _method=PUT com FormData
      await api.post(`/movies/${currentMovie.value.id}`, formData, config)
    } else {
      await api.post('/movies', formData, config)
    }
    showForm.value = false
    await fetchMovies()
  } catch (err) {
    error.value = 'Erro ao salvar filme: ' + (err.response?.data?.message || err.message)
  }
}

const confirmDelete = (movie) => {
  movieToDelete.value = movie
  showModal.value = true
}

const deleteMovie = async () => {
  try {
    await api.delete(`/movies/${movieToDelete.value.id}`)
    await fetchMovies()
  } catch (err) {
    error.value = 'Erro ao excluir filme: ' + (err.response?.data?.message || err.message)
  } finally {
    movieToDelete.value = null
  }
}
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
