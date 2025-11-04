import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../views/Home.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/Login.vue'),
    meta: { requiresAuth: false, guest: true }
  },
  {
    path: '/catalogo',
    name: 'Catalogo',
    component: () => import('../views/Catalogo.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('../views/Dashboard.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/perfil',
    name: 'Perfil',
    component: () => import('../views/Perfil.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/pessoas',
    name: 'Pessoas',
    component: () => import('../views/Pessoas.vue'),
    meta: { requiresAuth: true, role: 'admin' }
  },
  {
    path: '/filmes/gerenciar',
    name: 'GerenciarFilmes',
    component: () => import('../views/GerenciarFilmes.vue'),
    meta: { requiresAuth: true, role: 'admin' }
  },
  {
    path: '/locacoes',
    name: 'Locacoes',
    component: () => import('../views/Locacoes.vue'),
    meta: { requiresAuth: true, roles: ['admin', 'attendant'] }
  },
  {
    path: '/minhas-locacoes',
    name: 'MinhasLocacoes',
    component: () => import('../views/MinhasLocacoes.vue'),
    meta: { requiresAuth: true, role: 'customer' }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Verificar autenticação
  if (authStore.token && !authStore.isAuthenticated) {
    await authStore.checkAuth()
  }

  // Redirecionar usuários autenticados da página de login
  if (to.meta.guest && authStore.isAuthenticated) {
    return next({ name: 'Dashboard' })
  }

  // Verificar se a rota requer autenticação
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return next({ name: 'Login', query: { redirect: to.fullPath } })
  }

  // Verificar role específica
  if (to.meta.role && authStore.user?.role !== to.meta.role) {
    return next({ name: 'Dashboard' })
  }

  // Verificar múltiplas roles
  if (to.meta.roles && !to.meta.roles.includes(authStore.user?.role)) {
    return next({ name: 'Dashboard' })
  }

  next()
})

export default router
