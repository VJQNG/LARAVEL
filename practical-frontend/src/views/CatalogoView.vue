<script setup>
import { ref, computed, onMounted } from 'vue'
import { getProductos } from '../services/productoService'
// 1. Importamos Pinia y el Widget del Carrito
import { useCarritoStore } from '../stores/carrito'
import CartIcon from '../components/CartIcon.vue'

const productos = ref([])
const busqueda = ref('')
const carrito = useCarritoStore() // Conectamos el demonio

onMounted(async () => {
  try {
    const res = await getProductos()
    productos.value = res.data
  } catch (error) {
    console.error("Error al cargar el catálogo", error)
  }
})

const productosFiltrados = computed(() =>
  productos.value.filter(p =>
    p.nombre.toLowerCase().includes(busqueda.value.toLowerCase())
  )
)
</script>

<template>
  <div class="catalogo">
    <div class="cabecera-catalogo">
      <h2>Catálogo de Productos</h2>
      <CartIcon />
    </div>
    
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
        
        <router-link :to="`/catalogo/${producto.id}`" class="btn-detalle">Ver Detalle</router-link>
        
        <button @click="carrito.agregar(producto)" class="btn-agregar">
          <template v-if="carrito.cantidadDeProducto(producto.id) > 0">
            En carrito ({{ carrito.cantidadDeProducto(producto.id) }})
          </template>
          <template v-else>
            Agregar al carrito
          </template>
        </button>

      </div>
      <div v-if="productosFiltrados.length === 0">No se encontraron productos.</div>
    </div>
  </div>
</template>

<style scoped>
.cabecera-catalogo { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.buscador { width: 100%; padding: 10px; margin-bottom: 20px; box-sizing: border-box; }
.grid { display: flex; flex-wrap: wrap; gap: 20px; }
.card { background: #1e1e1e; padding: 15px; border-radius: 8px; width: 220px; text-align: center; display: flex; flex-direction: column; gap: 10px; }
.btn-detalle { display: inline-block; padding: 5px 10px; background: #555; color: #fff; text-decoration: none; border-radius: 4px; }
.btn-agregar { background: #38bdf8; color: #000; font-weight: bold; border: none; padding: 8px; cursor: pointer; border-radius: 4px; }
.btn-agregar:hover { background: #0284c7; }
</style>
