<template>
  <div class="home-container">
    <section class="hero">
      <div class="hero-content">
        <h1 class="hero-title">Bem-vindo ao Préâmbulo Movies</h1>
        <p class="hero-subtitle">Descubra os melhores filmes do cinema</p>
        <router-link to="/filmes" class="btn btn-primary">Explorar Catálogo</router-link>
      </div>
    </section>

    <section class="featured-films">
      <div class="container">
        <h2 class="section-title">Filmes em Destaque</h2>
        <div class="grid grid-4">
          <div v-for="filme in filmes" :key="filme.id" class="filme-card">
            <div class="filme-poster">
              <img v-if="filme.poster_url" :src="filme.poster_url" :alt="filme.titulo" />
              <div v-else class="placeholder">{{ filme.titulo }}</div>
            </div>
            <div class="filme-info">
              <h3 class="filme-titulo">{{ filme.titulo }}</h3>
              <p class="filme-genero">{{ filme.genero }}</p>
              <p class="filme-ano">{{ filme.ano }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { filmeService, Filme } from '../services/FilmeService';

const filmes = ref<Filme[]>([]);
const isLoading = ref(false);
const error = ref<string | null>(null);

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
.home-container {
  width: 100%;
}

.hero {
  background: linear-gradient(135deg, #1a1f3a 0%, #0a0e27 100%);
  padding: 6rem 2rem;
  text-align: center;
  border-bottom: 2px solid var(--color-primary);
}

.hero-content {
  max-width: 800px;
  margin: 0 auto;
}

.hero-title {
  font-size: var(--font-size-2xl);
  font-weight: 700;
  margin-bottom: var(--spacing-md);
  color: var(--color-primary);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.hero-subtitle {
  font-size: var(--font-size-lg);
  color: var(--color-text);
  margin-bottom: var(--spacing-lg);
}

.featured-films {
  padding: var(--spacing-2xl) 0;
}

.section-title {
  font-size: var(--font-size-xl);
  font-weight: 700;
  margin-bottom: var(--spacing-lg);
  color: var(--color-primary);
  text-transform: uppercase;
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

.filme-ano {
  font-size: var(--font-size-sm);
  color: var(--color-text);
}
</style>
