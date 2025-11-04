<template>
  <div class="dashboard">
    <div class="container">
      <h1>Dashboard</h1>
      <p>Bem-vindo, {{ authStore.user?.nome_completo || 'Usuário' }}!</p>
      <p>Role: {{ authStore.user?.role }}</p>
      
      <div class="dashboard-cards">
        <router-link v-if="authStore.isAdmin" to="/pessoas" class="card">
          <h3>Gerenciar Pessoas</h3>
          <p>Administrar usuários do sistema</p>
        </router-link>

        <router-link v-if="authStore.canManageMovies" to="/filmes/gerenciar" class="card">
          <h3>Gerenciar Filmes</h3>
          <p>Adicionar e editar filmes</p>
        </router-link>

        <router-link v-if="authStore.canManageRentals" to="/locacoes" class="card">
          <h3>Gerenciar Locações</h3>
          <p>Ver e criar locações</p>
        </router-link>

        <router-link v-if="authStore.isCustomer" to="/minhas-locacoes" class="card">
          <h3>Minhas Locações</h3>
          <p>Ver histórico de locações</p>
        </router-link>

        <router-link to="/catalogo" class="card">
          <h3>Catálogo</h3>
          <p>Ver filmes disponíveis</p>
        </router-link>

        <router-link to="/perfil" class="card">
          <h3>Meu Perfil</h3>
          <p>Editar informações pessoais</p>
        </router-link>
      </div>

      <button @click="handleLogout" class="btn btn-secondary">Sair</button>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const handleLogout = async () => {
  await authStore.logout()
  router.push({ name: 'Login' })
}
</script>

<style scoped>
.dashboard {
  padding: 100px 20px 40px;
  min-height: 100vh;
}

h1 {
  margin-bottom: 20px;
}

.dashboard-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
  margin: 40px 0;
}

.card {
  text-decoration: none;
  color: inherit;
}

.card h3 {
  margin-bottom: 10px;
  color: var(--primary-color);
}

.card p {
  color: var(--text-secondary);
  font-size: 14px;
}
</style>
