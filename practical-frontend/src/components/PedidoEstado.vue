<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

// Recibimos el ID del pedido que acabamos de crear
const props = defineProps(['pedidoId'])

const estado = ref('procesando')
const emailListo = ref(false)
let intervalo = null

onMounted(() => {
  // Iniciamos el ciclo de consultas cada 3 segundos
  intervalo = setInterval(async () => {
    try {
      // Reemplaza con la configuración de Axios que uses normalmente para los tokens
      const token = localStorage.getItem('token')
      const { data } = await axios.get(`http://localhost:8000/api/pedidos/${props.pedidoId}`, {
        headers: { Authorization: `Bearer ${token}` }
      })
      
      // Verificamos si la base de datos ya tiene la marca de tiempo del correo
      emailListo.value = !!data.email_enviado_at
      
      // Si ya se envió, destruimos el ciclo para no saturar el servidor
      if (emailListo.value) {
        clearInterval(intervalo)
      }
    } catch (error) {
      console.error("Error verificando el estado del pedido:", error)
    }
  }, 3000)
})

// Regla de oro de la RAM: Limpiar el intervalo si el componente se destruye
onUnmounted(() => {
  if (intervalo) clearInterval(intervalo)
})
</script>

<template>
  <div class="estado-container">
    <div v-if="!emailListo" class="estado procesando">
      ⏳ Procesando tu pedido en segundo plano...
    </div>
    <div v-else class="estado listo">
      ✅ ¡Pedido confirmado! Revisa tu bandeja de entrada en Mailtrap.
    </div>
  </div>
</template>

<style scoped>
.estado-container { margin-top: 20px; font-weight: bold; text-align: center; }
.estado { padding: 15px; border-radius: 5px; transition: all 0.3s ease; }
.procesando { background-color: #1e1e1e; color: #38bdf8; border: 1px solid #38bdf8; }
.listo { background-color: rgba(0, 255, 0, 0.1); color: #0f0; border: 1px solid #0f0; }
</style>
