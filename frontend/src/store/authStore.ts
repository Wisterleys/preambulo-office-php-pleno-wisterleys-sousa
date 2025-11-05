import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { authService, LoginRequest, AuthResponse } from '../services/AuthService';

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('token'));
  const user = ref<any>(null);
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  const isAuthenticated = computed(() => !!token.value);

  const login = async (credentials: LoginRequest) => {
    isLoading.value = true;
    error.value = null;

    try {
      const response: AuthResponse = await authService.login(credentials);
      token.value = response.token;
      localStorage.setItem('token', response.token);
      
      const userData = await authService.me();
      user.value = userData.dados;
      
      return response;
    } catch (err: any) {
      error.value = err.response?.data?.mensagem || 'Erro ao fazer login';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  const logout = async () => {
    try {
      await authService.logout();
    } catch (err) {
      console.error('Erro ao fazer logout:', err);
    } finally {
      token.value = null;
      user.value = null;
      localStorage.removeItem('token');
    }
  };

  const refreshToken = async () => {
    try {
      const response: AuthResponse = await authService.refresh();
      token.value = response.token;
      localStorage.setItem('token', response.token);
      return response;
    } catch (err) {
      await logout();
      throw err;
    }
  };

  return {
    token,
    user,
    isLoading,
    error,
    isAuthenticated,
    login,
    logout,
    refreshToken,
  };
});
