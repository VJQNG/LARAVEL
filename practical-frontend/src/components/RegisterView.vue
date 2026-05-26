<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const form = ref({ name: '', email: '', password: '', password_confirmation: '' })
const error = ref('')
const router = useRouter()
const auth = useAuthStore()

const manejarRegistro = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'Las contraseñas no coinciden.'
    return
  }
  try {
    await auth.register(form.value)
    router.push('/dashboard')
  } catch (e) {
    // Si Laravel nos responde con un rechazo 422 de validación
    if (e.response && e.response.status === 422) {
      // Extraemos el primer error específico que mandó el servidor
      const errores = e.response.data.errors
      error.value = Object.values(errores)[0][0]
    } else {
      error.value = 'Error de conexión con el servidor.'
    }
  }
}

</script>

<template>
  <div class="auth-box">
    <h2>Crear Cuenta Nueva</h2>
    <div v-if="error" class="alerta">{{ error }}</div>
    <form @submit.prevent="manejarRegistro">
      <input v-model="form.name" type="text" placeholder="Tu Nombre" required />
      <input v-model="form.email" type="email" placeholder="Correo electrónico" required />
      <input v-model="form.password" type="password" placeholder="Contraseña (mínimo 8 caracteres)" required />
      <input v-model="form.password_confirmation" type="password" placeholder="Confirmar contraseña" required />
      <button type="submit">Registrar e Ingresar</button>
    </form>
    <p>¿Ya tienes cuenta? <router-link to="/login">Entra aquí</router-link></p>
  </div>
</template>

<style scoped>
.auth-box { background: #1e1e1e; padding: 20px; border-radius: 8px; text-align: center; }
input { display: block; margin: 10px auto; padding: 10px; width: 80%; }
button { padding: 10px 20px; cursor: pointer; background: #38bdf8; border: none; color: #000; font-weight: bold; }
.alerta { color: #ff4444; margin-bottom: 10px; }
a { color: #38bdf8; }
</style>
