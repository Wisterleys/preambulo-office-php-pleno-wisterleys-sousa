<template>
  <div class="login-container">
    <div class="login-box">
      <h1 class="logo">Préambulo Movies</h1>
      <h2>Entrar</h2>
      
      <div v-if="error" class="alert alert-error">
        {{ error }}
      </div>

      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label class="form-label" for="email">Email</label>
          <input
            id="email"
            v-model="credentials.email"
            type="email"
            class="form-input"
            placeholder="seu@email.com"
            required
          />
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Senha</label>
          <input
            id="password"
            v-model="credentials.password"
            type="password"
            class="form-input"
            placeholder="••••••••"
            required
          />
        </div>

        <button
          type="submit"
          class="btn btn-primary btn-block"
          :disabled="loading"
        >
          {{ loading ? 'Entrando...' : 'Entrar' }}
        </button>
      </form>

      <p class="login-footer">
        Não tem uma conta? Entre em contato com o administrador.
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const credentials = ref({
  email: '',
  password: ''
})

const loading = ref(false)
const error = ref(null)

const handleLogin = async () => {
  loading.value = true
  error.value = null

  const success = await authStore.login(credentials.value)

  if (success) {
    router.push({ name: 'Dashboard' })
  } else {
    error.value = authStore.error
  }

  loading.value = false
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 100%);
  padding: 20px;
}

.login-box {
  background-color: var(--background-card);
  padding: 40px;
  border-radius: 8px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
}

.logo {
  color: var(--primary-color);
  text-align: center;
  margin-bottom: 10px;
  font-size: 28px;
}

h2 {
  text-align: center;
  margin-bottom: 30px;
  color: var(--text-primary);
}

.btn-block {
  width: 100%;
  margin-top: 10px;
}

.login-footer {
  text-align: center;
  margin-top: 20px;
  color: var(--text-secondary);
  font-size: 14px;
}
</style>
