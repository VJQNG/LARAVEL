<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../plugins/axios' // Importamos nuestro puente de red directo

// Recibimos el ID como propiedad (Paso 4.5)
const props = defineProps(['id'])
const router = useRouter()
const producto = ref(null)

onMounted(async () => {
  try {
    const res = await api.get(`/productos/${props.id}`)
    producto.value = res.data
  } catch (error) {
    console.error("Error al cargar los detalles", error)
  }
})

// Función para volver atrás en el historial (Paso 4.5)
const volver = () => {
  router.back()
}
</script>

<template>
  <div class="detalle" v-if="producto">
    <h2>{{ producto.nombre }}</h2>
    <p><strong>Descripción:</strong> {{ producto.descripcion || 'Sin descripción' }}</p>
    <p class="precio">Precio: ${{ producto.precio }}</p>
    <p><strong>Stock disponible:</strong> {{ producto.stock }} unidades</p>
    
    <button @click="volver" class="btn-volver">Volver al catálogo</button>
  </div>
  <div v-else>
    Cargando información...
  </div>
</template>

<style scoped>
.detalle { background: #1e1e1e; padding: 30px; border-radius: 8px; max-width: 500px; margin: 0 auto; }
.precio { font-size: 1.5em; color: #0f0; }
.btn-volver { margin-top: 20px; padding: 10px 20px; background: #555; color: white; border: none; cursor: pointer; border-radius: 4px; }
.btn-volver:hover { background: #777; }
</style>
