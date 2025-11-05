<template>
  <div id="app" class="app-container">
    <nav class="navbar navbar-dark bg-dark sticky-top">
      <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">
          <strong>Préâmbulo Movies</strong>
        </span>
        <div class="d-flex gap-2">
          <router-link to="/" class="btn btn-sm btn-outline-light">Home</router-link>
          <router-link v-if="!isAuthenticated" to="/login" class="btn btn-sm btn-primary">Login</router-link>
          <router-link v-if="isAuthenticated" to="/dashboard" class="btn btn-sm btn-outline-light">Dashboard</router-link>
          <button v-if="isAuthenticated" @click="logout" class="btn btn-sm btn-danger">Logout</button>
        </div>
      </div>
    </nav>

    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from './store/authStore';

const router = useRouter();
const authStore = useAuthStore();

const isAuthenticated = computed(() => authStore.isAuthenticated);

const logout = async () => {
  await authStore.logout();
  router.push('/');
};
</script>

<style scoped>
.app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background-color: #0a0e27;
  color: #ffffff;
}

.main-content {
  flex: 1;
  padding: 2rem 0;
}

.navbar {
  background-color: #1a1f3a !important;
  border-bottom: 1px solid #2d3561;
}

.navbar-brand {
  font-size: 1.5rem;
  color: #00d4ff !important;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
</style>
