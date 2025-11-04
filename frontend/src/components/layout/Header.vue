<template>
  <header class="header">
    <div class="container">
      <div class="header-content">
        <router-link to="/" class="logo">
          <span class="logo-icon">🎬</span>
          <span class="logo-text">Preâmbulo <span class="logo-highlight">Movies</span></span>
        </router-link>
        
        <nav class="nav">
          <template v-if="authStore.isClient">
            <router-link to="/client/dashboard">Catálogo</router-link>
            <router-link to="/client/rentals">Minhas Locações</router-link>
          </template>

          <template v-if="authStore.isAttendant || authStore.isAdmin">
            <router-link to="/attendant/dashboard">Dashboard</router-link>
            <router-link to="/attendant/clients">Clientes</router-link>
            <router-link to="/attendant/rentals">Locações</router-link>
          </template>

          <template v-if="authStore.isAdmin">
            <router-link to="/admin/dashboard">Dashboard</router-link>
            <router-link to="/admin/movies">Filmes</router-link>
            <router-link to="/admin/reports">Relatórios</router-link>
            <router-link to="/admin/today-returns">Devoluções Hoje</router-link>
          </template>

          <span>{{ authStore.user?.name }}</span>
          <button @click="handleLogout" class="btn btn-secondary">Sair</button>
        </nav>
      </div>
    </div>
  </header>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
