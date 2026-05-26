<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const form = ref({ email: '', password: '' })
const error = ref('')
const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

const manejarLogin = async () => {
  try {
    await auth.login(form.value)
    
    // Verificamos si había una redirección pendiente, si no, vamos a /admin
    const redirectPath = route.query.redirect || '/admin'
    router.push(redirectPath)
    
  } catch (e) {
    error.value = 'Credenciales incorrectas. El servidor rechazó el acceso.'
  }
}
</script>

<template>
  <div class="auth-box">
    <h2>Iniciar Sesión</h2>
    <div v-if="error" class="alerta">{{ error }}</div>
    <form @submit.prevent="manejarLogin">
      <input v-model="form.email" type="email" placeholder="Correo electrónico" required />
      <input v-model="form.password" type="password" placeholder="Contraseña" required />
      <button type="submit">Entrar</button>
    </form>
    <p>¿No tienes cuenta? <router-link to="/register">Regístrate aquí</router-link></p>
  </div>
</template>

<style scoped>
.auth-box { background: #1e1e1e; padding: 20px; border-radius: 8px; text-align: center; }
input { display: block; margin: 10px auto; padding: 10px; width: 80%; }
button { padding: 10px 20px; cursor: pointer; background: #38bdf8; border: none; color: #000; font-weight: bold; }
.alerta { color: #ff4444; margin-bottom: 10px; }
a { color: #38bdf8; }
</style>
