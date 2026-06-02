<script setup>
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

const logout = async () => {
  await auth.logout()
  router.push('/login')
}
</script>

<template>
  <div class="admin-layout">
    <aside class="sidebar">
      <h2>Tienda Arch</h2>
      <p class="user-name">Sesión de: <br><strong>{{ auth.user?.name }}</strong></p>
      
      <nav>
        <router-link to="/admin" exact-active-class="activo">Dashboard</router-link>
        <router-link to="/admin/productos" active-class="activo">Inventario</router-link>
        <router-link to="/admin/nuevo" active-class="activo">Nuevo Producto</router-link>
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
