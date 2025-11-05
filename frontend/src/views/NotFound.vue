<template>
  <div class="not-found-container">
    <div class="not-found-content">
      <h1 class="not-found-title">404</h1>
      <p class="not-found-subtitle">Página não encontrada</p>
      <p class="not-found-description">Desculpe, a página que você está procurando não existe.</p>
      <router-link to="/" class="btn btn-primary">Voltar para Home</router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
// Component logic here if needed
</script>

<style scoped>
.not-found-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: calc(100vh - 80px);
  padding: var(--spacing-lg);
}

.not-found-content {
  text-align: center;
}

.not-found-title {
  font-size: 6rem;
  font-weight: 700;
  color: var(--color-primary);
  margin-bottom: var(--spacing-md);
}

.not-found-subtitle {
  font-size: var(--font-size-2xl);
  font-weight: 600;
  color: var(--color-light);
  margin-bottom: var(--spacing-md);
}

.not-found-description {
  font-size: var(--font-size-lg);
  color: var(--color-text);
  margin-bottom: var(--spacing-lg);
}
</style>
