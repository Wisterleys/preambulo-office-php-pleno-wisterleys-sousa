<template>
  <div class="home">
    <header class="hero">
      <div class="container">
        <h1>Bem-vindo ao Préambulo Movies</h1>
        <p>Seu sistema de locação de filmes</p>
        <div class="hero-buttons">
          <router-link to="/catalogo" class="btn btn-primary">
            Ver Catálogo
          </router-link>
          <router-link v-if="!authStore.isAuthenticated" to="/login" class="btn btn-secondary">
            Entrar
          </router-link>
          <router-link v-else to="/dashboard" class="btn btn-secondary">
            Dashboard
          </router-link>
        </div>
      </div>
    </header>
  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
</script>

<style scoped>
.hero {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 100%);
  text-align: center;
}

h1 {
  font-size: 48px;
  margin-bottom: 20px;
  background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

p {
  font-size: 20px;
  color: var(--text-secondary);
  margin-bottom: 40px;
}

.hero-buttons {
  display: flex;
  gap: 20px;
  justify-content: center;
}

@media (max-width: 768px) {
  h1 {
    font-size: 32px;
  }

  p {
    font-size: 16px;
  }

  .hero-buttons {
    flex-direction: column;
    align-items: center;
  }
}
</style>
