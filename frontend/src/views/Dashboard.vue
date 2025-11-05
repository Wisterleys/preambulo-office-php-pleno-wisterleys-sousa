<template>
  <div class="dashboard-container">
    <div class="container">
      <h1 class="dashboard-title">Dashboard</h1>
      <p class="dashboard-subtitle">Bem-vindo, {{ user?.name || 'Usuário' }}</p>

      <div class="grid grid-2">
        <div class="dashboard-card">
          <h2 class="card-title">Minhas Locações</h2>
          <p class="card-description">Acompanhe seus filmes alugados</p>
          <router-link to="/minhas-locacoes" class="btn btn-primary">Ver Locações</router-link>
        </div>

        <div class="dashboard-card">
          <h2 class="card-title">Perfil</h2>
          <p class="card-description">Edite suas informações pessoais</p>
          <router-link to="/perfil" class="btn btn-primary">Editar Perfil</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useAuthStore } from '../store/authStore';

const authStore = useAuthStore();
const user = computed(() => authStore.user?.user);
</script>

<style scoped>
.dashboard-container {
  padding: var(--spacing-2xl) 0;
}

.dashboard-title {
  font-size: var(--font-size-2xl);
  font-weight: 700;
  color: var(--color-primary);
  margin-bottom: var(--spacing-sm);
  text-transform: uppercase;
}

.dashboard-subtitle {
  font-size: var(--font-size-lg);
  color: var(--color-text);
  margin-bottom: var(--spacing-2xl);
}

.dashboard-card {
  background-color: var(--color-secondary);
  border: 1px solid var(--color-border);
  border-radius: var(--border-radius-lg);
  padding: var(--spacing-lg);
  transition: all var(--transition-base);
}

.dashboard-card:hover {
  border-color: var(--color-primary);
  box-shadow: var(--shadow-lg);
}

.card-title {
  font-size: var(--font-size-lg);
  font-weight: 600;
  color: var(--color-light);
  margin-bottom: var(--spacing-sm);
}

.card-description {
  font-size: var(--font-size-base);
  color: var(--color-text);
  margin-bottom: var(--spacing-lg);
}
</style>
