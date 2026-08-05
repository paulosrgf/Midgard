import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth' // Importamos o Pinia
import HomeView from '../views/HomeView.vue'
import RegisterView from '../views/RegisterView.vue'
import LoginView from '../views/LoginView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
      // Essa tag 'meta' avisa que a página é restrita
      meta: { requiresAuth: true },
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
      meta: { requiresGuest: true }, // Apenas para visitantes
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: { requiresGuest: true }, // Apenas para visitantes
    },
  ],
})

// O Guarda de Rota: Roda ANTES de cada navegação
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const isAuthenticated = authStore.token !== null

  if (to.meta.requiresAuth && !isAuthenticated) {
    // Tenta acessar página restrita sem estar logado -> vai pro Login
    next('/login')
  } else if (to.meta.requiresGuest && isAuthenticated) {
    // Tenta acessar login/registro já estando logado -> vai pro Feed
    next('/')
  } else {
    // Tudo certo, pode passar
    next()
  }
})

export default router
