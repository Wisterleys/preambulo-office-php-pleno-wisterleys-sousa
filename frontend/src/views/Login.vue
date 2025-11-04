<template>
  <div class="login-container">
    <div class="login-box">
      <div class="login-logo">
        <span class="logo-icon">🎬</span>
        <span>Preâmbulo <span class="logo-highlight">Movies</span></span>
      </div>
      
      <div v-if="error" class="error">{{ error }}</div>
      
      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label>Tipo de Usuário</label>
          <select v-model="userType" required>
            <option value="user">Funcionário (Admin/Atendente)</option>
            <option value="client">Cliente</option>
          </select>
        </div>

        <div class="form-group">
          <label>E-mail</label>
          <input type="email" v-model="email" required />
        </div>

        <div class="form-group">
          <label>Senha</label>
          <input type="password" v-model="password" required />
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%">
          Entrar
        </button>
      </form>

      <div style="margin-top: 20px; font-size: 12px; color: var(--text-secondary)">
        <p><strong>Credenciais de teste:</strong></p>
        <p>Admin: admin@preambulomovies.com / admin123</p>
        <p>Atendente: atendente@preambulomovies.com / atendente123</p>
        <p>Cliente: joao@example.com / cliente123</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
// import { getCsrfCookie } from '../services/api' // Removido

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const userType = ref('user')
const error = ref('')

const handleLogin = async () => {
  try {
    error.value = ''
    
    // 1. Obter o cookie CSRF (Removido, pois o login não precisa de CSRF)
    // await getCsrfCookie()

    // 2. Tentar o login
    await authStore.login(email.value, password.value, userType.value)
    
    if (authStore.isAdmin) {
      router.push('/admin/dashboard')
    } else if (authStore.isAttendant) {
      router.push('/attendant/dashboard')
    } else {
      router.push('/client/dashboard')
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao fazer login'
  }
}
</script>
