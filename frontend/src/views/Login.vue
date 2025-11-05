<template>
  <div class="login-container">
    <div class="login-card">
      <h1 class="login-title">Entrar</h1>
      <p class="login-subtitle">Acesse sua conta Préâmbulo Movies</p>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input
            id="email"
            v-model="email"
            type="email"
            placeholder="seu@email.com"
            class="form-control"
            required
          />
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Senha</label>
          <input
            id="password"
            v-model="password"
            type="password"
            placeholder="Sua senha"
            class="form-control"
            required
          />
        </div>

        <div v-if="error" class="alert alert-danger">
          {{ error }}
        </div>

        <button type="submit" class="btn btn-primary w-full" :disabled="isLoading">
          {{ isLoading ? 'Entrando...' : 'Entrar' }}
        </button>
      </form>

      <p class="login-footer">
        Não tem conta? <router-link to="/cadastro">Cadastre-se aqui</router-link>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../store/authStore';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const isLoading = ref(false);
const error = ref<string | null>(null);

const handleLogin = async () => {
  isLoading.value = true;
  error.value = null;

  try {
    await authStore.login({ email: email.value, password: password.value });
    router.push('/dashboard');
  } catch (err: any) {
    error.value = err.response?.data?.mensagem || 'Erro ao fazer login';
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.login-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: calc(100vh - 80px);
  padding: var(--spacing-lg);
}

.login-card {
  background-color: var(--color-secondary);
  border: 1px solid var(--color-border);
  border-radius: var(--border-radius-lg);
  padding: var(--spacing-2xl);
  max-width: 400px;
  width: 100%;
  box-shadow: var(--shadow-xl);
}

.login-title {
  font-size: var(--font-size-2xl);
  font-weight: 700;
  color: var(--color-primary);
  margin-bottom: var(--spacing-sm);
  text-align: center;
}

.login-subtitle {
  font-size: var(--font-size-base);
  color: var(--color-text);
  text-align: center;
  margin-bottom: var(--spacing-lg);
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-lg);
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-sm);
}

.form-label {
  font-size: var(--font-size-sm);
  font-weight: 600;
  color: var(--color-text);
}

.form-control {
  padding: var(--spacing-md) var(--spacing-lg);
  background-color: var(--color-dark);
  border: 1px solid var(--color-border);
  border-radius: var(--border-radius);
  color: var(--color-text);
  font-size: var(--font-size-base);
  transition: all var(--transition-fast);
}

.form-control:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
}

.form-control::placeholder {
  color: var(--color-border);
}

.w-full {
  width: 100%;
}

.alert {
  padding: var(--spacing-md);
  border-radius: var(--border-radius);
  font-size: var(--font-size-sm);
}

.alert-danger {
  background-color: rgba(239, 68, 68, 0.1);
  border: 1px solid var(--color-danger);
  color: var(--color-danger);
}

.login-footer {
  text-align: center;
  font-size: var(--font-size-sm);
  color: var(--color-text);
  margin-top: var(--spacing-lg);
}

.login-footer a {
  color: var(--color-primary);
  font-weight: 600;
}

.login-footer a:hover {
  text-decoration: underline;
}
</style>
