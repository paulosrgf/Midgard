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
router.beforeEach((to, from) => {
  const authStore = useAuthStore()
  const isAuthenticated = authStore.token !== null

  if (to.meta.requiresAuth && !isAuthenticated) {
    return '/login' // Em vez de next(), apenas retornamos o caminho
  } else if (to.meta.requiresGuest && isAuthenticated) {
    return '/'
  }

  // Se não bater em nenhum IF e não retornar nada, o Vue entende que está liberado!
})

export default router
