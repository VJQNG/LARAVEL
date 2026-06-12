<script setup>
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import AdminNotificaciones from '../components/AdminNotificaciones.vue'

const auth = useAuthStore()
const router = useRouter()

const logout = async () => {
  await auth.logout()
  router.push('/login')
}
</script>

<template>
  <AdminNotificaciones />
  <div class="admin-layout">
    <aside class="sidebar">
      <h2>Tienda Arch</h2>
      <p class="user-name">Sesión de: <br><strong>{{ auth.user?.name }}</strong></p>
      <span style="color: #38bdf8; font-size: 0.85em;">Rol: {{ auth.user?.rol }}</span>
      
      <nav>
        <router-link to="/catalogo" class="btn-menu">Ver Catálogo</router-link>
        <router-link to="/admin" exact-active-class="activo">Dashboard</router-link>
        <router-link to="/admin/productos" active-class="activo">Inventario</router-link>
        <router-link to="/admin/nuevo" class="btn-menu" v-can="'crear'">Nuevo Producto</router-link>
        <router-link to="/admin/categorias" class="btn-menu">Categorías</router-link>
      </nav>

      <button @click="logout" class="btn-logout">Cerrar Sesión</button>
    </aside>

    <main class="content">
      <router-view /> </main>
  </div>
</template>

<style scoped>
.admin-layout { display: flex; min-height: 100vh; }
.sidebar { width: 250px; background: #1e1e1e; padding: 20px; display: flex; flex-direction: column; border-right: 1px solid #333; }
.sidebar h2 { color: #38bdf8; margin-top: 0; }
.user-name { color: #aaa; margin-bottom: 30px; font-size: 0.9em; }
nav { display: flex; flex-direction: column; gap: 10px; flex-grow: 1; }
nav a { color: #fff; text-decoration: none; padding: 10px; border-radius: 4px; transition: background 0.3s; }
nav a:hover { background: #333; }
nav a.activo { background: #38bdf8; color: #000; font-weight: bold; }
.btn-logout { margin-top: auto; background: #ff4444; color: white; border: none; padding: 10px; cursor: pointer; border-radius: 4px; font-weight: bold; }
.content { flex-grow: 1; padding: 30px; }
</style>
