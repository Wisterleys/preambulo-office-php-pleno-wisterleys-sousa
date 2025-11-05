<template>
  <div class="perfil-container">
    <div class="container">
      <h1 class="perfil-title">Meu Perfil</h1>

      <div class="perfil-card">
        <form @submit.prevent="handleUpdatePerfil" class="perfil-form">
          <div class="form-group">
            <label for="name" class="form-label">Nome</label>
            <input
              id="name"
              v-model="formData.name"
              type="text"
              placeholder="Seu nome"
              class="form-control"
            />
          </div>

          <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input
              id="email"
              v-model="formData.email"
              type="email"
              placeholder="seu@email.com"
              class="form-control"
              disabled
            />
          </div>

          <div v-if="error" class="alert alert-danger">
            {{ error }}
          </div>

          <div v-if="success" class="alert alert-success">
            Perfil atualizado com sucesso!
          </div>

          <button type="submit" class="btn btn-primary" :disabled="isLoading">
            {{ isLoading ? 'Atualizando...' : 'Atualizar Perfil' }}
          </button>
        </form>
      </div>

      <div class="perfil-card mt-lg">
        <h2 class="card-title">Alterar Senha</h2>
        <form @submit.prevent="handleChangePassword" class="perfil-form">
          <div class="form-group">
            <label for="current-password" class="form-label">Senha Atual</label>
            <input
              id="current-password"
              v-model="passwordData.current"
              type="password"
              placeholder="Sua senha atual"
              class="form-control"
            />
          </div>

          <div class="form-group">
            <label for="new-password" class="form-label">Nova Senha</label>
            <input
              id="new-password"
              v-model="passwordData.new"
              type="password"
              placeholder="Nova senha"
              class="form-control"
            />
          </div>

          <div class="form-group">
            <label for="confirm-password" class="form-label">Confirmar Senha</label>
            <input
              id="confirm-password"
              v-model="passwordData.confirm"
              type="password"
              placeholder="Confirmar nova senha"
              class="form-control"
            />
          </div>

          <div v-if="passwordError" class="alert alert-danger">
            {{ passwordError }}
          </div>

          <div v-if="passwordSuccess" class="alert alert-success">
            Senha alterada com sucesso!
          </div>

          <button type="submit" class="btn btn-primary" :disabled="isLoadingPassword">
            {{ isLoadingPassword ? 'Alterando...' : 'Alterar Senha' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../store/authStore';
import { perfilService } from '../services/PerfilService';

const authStore = useAuthStore();

const formData = ref({
  name: '',
  email: '',
});

const passwordData = ref({
  current: '',
  new: '',
  confirm: '',
});

const isLoading = ref(false);
const isLoadingPassword = ref(false);
const error = ref<string | null>(null);
const success = ref(false);
const passwordError = ref<string | null>(null);
const passwordSuccess = ref(false);

onMounted(async () => {
  try {
    const perfil = await perfilService.getPerfil();
    formData.value.name = perfil.user.name;
    formData.value.email = perfil.user.email;
  } catch (err: any) {
    error.value = 'Erro ao carregar perfil';
  }
});

const handleUpdatePerfil = async () => {
  isLoading.value = true;
  error.value = null;
  success.value = false;

  try {
    await perfilService.updatePerfil({
      user: {
        id: authStore.user?.user?.id || '',
        name: formData.value.name,
        email: formData.value.email,
      },
      pessoa: authStore.user?.pessoa || {},
    });
    success.value = true;
  } catch (err: any) {
    error.value = err.response?.data?.mensagem || 'Erro ao atualizar perfil';
  } finally {
    isLoading.value = false;
  }
};

const handleChangePassword = async () => {
  if (passwordData.value.new !== passwordData.value.confirm) {
    passwordError.value = 'As senhas não coincidem';
    return;
  }

  isLoadingPassword.value = true;
  passwordError.value = null;
  passwordSuccess.value = false;

  try {
    await perfilService.updatePassword(passwordData.value.current, passwordData.value.new);
    passwordSuccess.value = true;
    passwordData.value = { current: '', new: '', confirm: '' };
  } catch (err: any) {
    passwordError.value = err.response?.data?.mensagem || 'Erro ao alterar senha';
  } finally {
    isLoadingPassword.value = false;
  }
};
</script>

<style scoped>
.perfil-container {
  padding: var(--spacing-2xl) 0;
}

.perfil-title {
  font-size: var(--font-size-2xl);
  font-weight: 700;
  color: var(--color-primary);
  margin-bottom: var(--spacing-lg);
  text-transform: uppercase;
}

.perfil-card {
  background-color: var(--color-secondary);
  border: 1px solid var(--color-border);
  border-radius: var(--border-radius-lg);
  padding: var(--spacing-lg);
  max-width: 600px;
}

.perfil-form {
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

.form-control:disabled {
  background-color: var(--color-border);
  cursor: not-allowed;
  opacity: 0.5;
}

.form-control::placeholder {
  color: var(--color-border);
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

.alert-success {
  background-color: rgba(16, 185, 129, 0.1);
  border: 1px solid var(--color-success);
  color: var(--color-success);
}

.card-title {
  font-size: var(--font-size-lg);
  font-weight: 600;
  color: var(--color-light);
  margin-bottom: var(--spacing-lg);
}

.mt-lg {
  margin-top: var(--spacing-lg);
}
</style>
