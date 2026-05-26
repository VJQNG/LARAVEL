<script setup>
import { ref, computed, onMounted } from 'vue'
import { getProductos } from '../services/productoService'

const productos = ref([])
const busqueda = ref('') // Variable conectada al input de búsqueda

onMounted(async () => {
  try {
    const res = await getProductos()
    productos.value = res.data
  } catch (error) {
    console.error("Error al cargar el catálogo", error)
  }
})

// Filtrado reactivo con computed (Paso 4.4)
const productosFiltrados = computed(() =>
  productos.value.filter(p =>
    p.nombre.toLowerCase().includes(busqueda.value.toLowerCase())
  )
)
</script>

<template>
  <div class="catalogo">
    <h2>Catálogo de Productos</h2>
    
    <input 
      v-model="busqueda" 
      type="text" 
      placeholder="Buscar por nombre..." 
      class="buscador"
    />

    <div class="grid">
      <div v-for="producto in productosFiltrados" :key="producto.id" class="card">
        <h3>{{ producto.nombre }}</h3>
        <p>${{ producto.precio }}</p>
        <router-link :to="`/catalogo/${producto.id}`" class="btn">Ver Detalle</router-link>
      </div>
      <div v-if="productosFiltrados.length === 0">No se encontraron productos.</div>
    </div>
  </div>
</template>

<style scoped>
.buscador { width: 100%; padding: 10px; margin-bottom: 20px; box-sizing: border-box; }
.grid { display: flex; flex-wrap: wrap; gap: 20px; }
.card { background: #1e1e1e; padding: 15px; border-radius: 8px; width: 220px; text-align: center; }
.btn { display: inline-block; margin-top: 10px; padding: 5px 10px; background: #38bdf8; color: #000; text-decoration: none; border-radius: 4px; }
</style>
