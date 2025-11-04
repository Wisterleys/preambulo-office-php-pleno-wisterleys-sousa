<template>
  <div id="app">
    <Header v-if="authStore.isAuthenticated" />
    <router-view />
  </div>
</template>

<script setup>
import { useAuthStore } from './stores/auth'
import Header from './components/layout/Header.vue'

const authStore = useAuthStore()
</script>
