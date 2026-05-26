import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

// Importaremos estos componentes en el próximo paso
import LoginView from '../components/LoginView.vue'
import RegisterView from '../components/RegisterView.vue'
import DashboardView from '../components/DashboardView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/dashboard' },
    { path: '/login', name: 'login', component: LoginView },
    { path: '/register', name: 'register', component: RegisterView },
    { 
      path: '/dashboard', 
      name: 'dashboard', 
      component: DashboardView,
      meta: { requiresAuth: true } // Esta etiqueta marca la ruta como "Zona Restringida"
    }
  ]
})

// El "Guard" moderno (Vue Router v4)
router.beforeEach((to) => {
  const auth = useAuthStore()

  // Si la ruta es privada y no hay llave, retorna hacia el login
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login' }
  }
  // Si no entra al if, la navegación continúa automáticamente
})

export default router
