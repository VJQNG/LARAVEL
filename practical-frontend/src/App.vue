<script setup>
import { useAuthStore } from './stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

const salir = async () => {
  await auth.logout() // Destruimos la sesión
  router.push('/login') // Te mandamos a la calle
}
</script>

<template>
  <main>
    <nav v-if="auth.isAuthenticated" class="navbar">
      <span>Bienvenido, {{ auth.user?.name }}</span>
      <button @click="salir">Cerrar Sesión</button>
    </nav>
    
    <router-view />
  </main>
</template>

<style scoped>
.navbar { 
  display: flex; 
  justify-content: space-between; 
  padding: 15px; 
  background: #0f172a; 
  color: #38bdf8;
  margin-bottom: 20px;
  border-radius: 5px;
}
button { cursor: pointer; padding: 5px 10px; }
main { max-width: 800px; margin: 0 auto; padding: 20px; font-family: sans-serif; }
</style>
