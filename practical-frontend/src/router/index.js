import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  // Rutas Públicas
  { path: '/', component: () => import('../views/HomeView.vue') },
  { path: '/catalogo', component: () => import('../views/CatalogoView.vue') },
  { path: '/catalogo/:id', component: () => import('../views/ProductoDetalle.vue'), props: true },
  { path: '/login', name: 'login', component: () => import('../components/LoginView.vue') }, // Reutilizamos tu Login
  
  // Rutas Privadas Anidadas (Admin)
  {
    path: '/admin',
    component: () => import('../layouts/AdminLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      { path: '', component: () => import('../views/admin/Dashboard.vue') },
      { path: 'productos', component: () => import('../views/admin/Productos.vue') },
      { path: 'nuevo', component: () => import('../views/admin/NuevoProducto.vue') }
    ]
  },

  // Ruta Catch-All (Error 404)
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: () => import('../views/NotFound.vue') }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Guard Global de Navegación (El PAM de Vue)
router.beforeEach(async (to) => {
  const auth = useAuthStore()
  
  // Inicializar usuario si hay token guardado (Paso 4.7)
  if (auth.token && !auth.user) {
    // Ejecutaremos la función fetchUser que crearemos después en Pinia
    if(typeof auth.fetchUser === 'function') {
      await auth.fetchUser()
    }
  }

  // Si la ruta es privada y no tiene llave, lo mandamos al login guardando a dónde iba (Paso 4.7)
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { path: '/login', query: { redirect: to.fullPath } }
  }
})

export default router
