<template>
  <div class="catalogue-container">
    <div class="container">
      <h1 class="catalogue-title">Catálogo de Filmes</h1>

      <div class="filters">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar filmes..."
          class="search-input"
        />
      </div>

      <div v-if="isLoading" class="loading">
        <p>Carregando filmes...</p>
      </div>

      <div v-else-if="error" class="alert alert-danger">
        {{ error }}
      </div>

      <div v-else-if="filteredFilmes.length > 0" class="grid grid-4">
        <div v-for="filme in filteredFilmes" :key="filme.id" class="filme-card">
          <div class="filme-poster">
            <img v-if="filme.poster_url" :src="filme.poster_url" :alt="filme.titulo" />
            <div v-else class="placeholder">{{ filme.titulo }}</div>
          </div>
          <div class="filme-info">
            <h3 class="filme-titulo">{{ filme.titulo }}</h3>
            <p class="filme-genero">{{ filme.genero }}</p>
            <p class="filme-ano">{{ filme.ano }}</p>
            <p class="filme-duracao">{{ filme.duracao }} min</p>
            <button class="btn btn-primary w-full mt-md">Alugar</button>
          </div>
        </div>
      </div>

      <div v-else class="empty-state">
        <p>Nenhum filme encontrado</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { filmeService, Filme } from '../services/FilmeService';

const filmes = ref<Filme[]>([]);
const searchQuery = ref('');
const isLoading = ref(false);
const error = ref<string | null>(null);

const filteredFilmes = computed(() => {
  return filmes.value.filter((filme) =>
    filme.titulo.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    filme.genero.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

onMounted(async () => {
  isLoading.value = true;
  try {
    filmes.value = await filmeService.getAllFilmes();
  } catch (err: any) {
    error.value = err.message || 'Erro ao carregar filmes';
  } finally {
    isLoading.value = false;
  }
});
</script>

<style scoped>
.catalogue-container {
  padding: var(--spacing-2xl) 0;
}

.catalogue-title {
  font-size: var(--font-size-2xl);
  font-weight: 700;
  color: var(--color-primary);
  margin-bottom: var(--spacing-lg);
  text-transform: uppercase;
}

.filters {
  margin-bottom: var(--spacing-2xl);
}

.search-input {
  width: 100%;
  max-width: 400px;
  padding: var(--spacing-md) var(--spacing-lg);
  background-color: var(--color-secondary);
  border: 1px solid var(--color-border);
  border-radius: var(--border-radius);
  color: var(--color-text);
  font-size: var(--font-size-base);
}

.search-input:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
}

.search-input::placeholder {
  color: var(--color-border);
}

.loading,
.empty-state {
  text-align: center;
  padding: var(--spacing-2xl);
  color: var(--color-text);
  font-size: var(--font-size-lg);
}

.alert {
  padding: var(--spacing-lg);
  border-radius: var(--border-radius);
  margin-bottom: var(--spacing-lg);
}

.alert-danger {
  background-color: rgba(239, 68, 68, 0.1);
  border: 1px solid var(--color-danger);
  color: var(--color-danger);
}

.filme-card {
  background-color: var(--color-secondary);
  border-radius: var(--border-radius-lg);
  overflow: hidden;
  cursor: pointer;
  transition: all var(--transition-base);
  box-shadow: var(--shadow-md);
}

.filme-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-xl);
  border: 1px solid var(--color-primary);
}

.filme-poster {
  width: 100%;
  aspect-ratio: 2/3;
  background-color: var(--color-border);
  overflow: hidden;
  position: relative;
}

.filme-poster img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #2d3561 0%, #1a1f3a 100%);
  color: var(--color-text);
  font-size: var(--font-size-sm);
  text-align: center;
  padding: var(--spacing-md);
}

.filme-info {
  padding: var(--spacing-md);
}

.filme-titulo {
  font-size: var(--font-size-base);
  font-weight: 600;
  color: var(--color-light);
  margin-bottom: var(--spacing-sm);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.filme-genero {
  font-size: var(--font-size-sm);
  color: var(--color-primary);
  margin-bottom: var(--spacing-xs);
}

.filme-ano,
.filme-duracao {
  font-size: var(--font-size-sm);
  color: var(--color-text);
}

.w-full {
  width: 100%;
}

.mt-md {
  margin-top: var(--spacing-md);
}
</style>
