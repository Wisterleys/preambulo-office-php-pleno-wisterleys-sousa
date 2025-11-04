import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    isAuthenticated: false,
    loading: false,
    error: null
  }),

  getters: {
    isAdmin: (state) => state.user?.role === 'admin',
    isAttendant: (state) => state.user?.role === 'attendant',
    isCustomer: (state) => state.user?.role === 'customer',
    canManageMovies: (state) => state.user?.role === 'admin',
    canManageRentals: (state) => ['admin', 'attendant'].includes(state.user?.role)
  },

  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/api/login', credentials)
        
        if (response.data.sucesso) {
          this.token = response.data.token
          this.user = response.data.usuario
          this.isAuthenticated = true
          
          localStorage.setItem('token', this.token)
          axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
          
          return true
        }
      } catch (error) {
        this.error = error.response?.data?.mensagem || 'Erro ao fazer login'
        return false
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        await axios.post('/api/logout')
      } catch (error) {
        console.error('Erro ao fazer logout:', error)
      } finally {
        this.token = null
        this.user = null
        this.isAuthenticated = false
        localStorage.removeItem('token')
        delete axios.defaults.headers.common['Authorization']
      }
    },

    async checkAuth() {
      if (!this.token) {
        return false
      }

      try {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        const response = await axios.get('/api/me')
        
        if (response.data.sucesso) {
          this.user = response.data.dados.pessoa
          this.isAuthenticated = true
          return true
        }
      } catch (error) {
        this.logout()
        return false
      }
    },

    async refreshToken() {
      try {
        const response = await axios.post('/api/refresh')
        
        if (response.data.sucesso) {
          this.token = response.data.token
          localStorage.setItem('token', this.token)
          axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
          return true
        }
      } catch (error) {
        this.logout()
        return false
      }
    }
  }
})
