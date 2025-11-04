import { defineStore } from 'pinia'
import api from '../services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    userType: localStorage.getItem('userType') || null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
    isAttendant: (state) => state.user?.role === 'attendant',
    isClient: (state) => state.userType === 'client',
  },

  actions: {
    async login(email, password, type) {
      const response = await api.post('/login', { email, password, type })
      this.token = response.data.access_token
      this.user = response.data.user
      this.userType = response.data.type
      
      localStorage.setItem('token', this.token)
      localStorage.setItem('user', JSON.stringify(this.user))
      localStorage.setItem('userType', this.userType)
    },

    async logout() {
      try {
        await api.post('/logout')
      } catch (error) {
        console.error('Erro ao fazer logout:', error)
      } finally {
        this.user = null
        this.token = null
        this.userType = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        localStorage.removeItem('userType')
      }
    },
  },
})
