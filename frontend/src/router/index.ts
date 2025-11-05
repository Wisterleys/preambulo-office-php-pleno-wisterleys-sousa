import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import { useAuthStore } from '../store/authStore';
import Home from '../views/Home.vue';
import Login from '../views/Login.vue';
import Dashboard from '../views/Dashboard.vue';
import CatalogueFilmes from '../views/CatalogueFilmes.vue';
import Perfil from '../views/Perfil.vue';
import MinhasLocacoes from '../views/MinhasLocacoes.vue';
import NotFound from '../views/NotFound.vue';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'Home',
    component: Home,
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/filmes',
    name: 'CatalogueFilmes',
    component: CatalogueFilmes,
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  {
    path: '/perfil',
    name: 'Perfil',
    component: Perfil,
    meta: { requiresAuth: true },
  },
  {
    path: '/minhas-locacoes',
    name: 'MinhasLocacoes',
    component: MinhasLocacoes,
    meta: { requiresAuth: true },
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFound,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  const requiresAuth = to.meta.requiresAuth as boolean;

  if (requiresAuth && !authStore.isAuthenticated) {
    next('/login');
  } else {
    next();
  }
});

export default router;
