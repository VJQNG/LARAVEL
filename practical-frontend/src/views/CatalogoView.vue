<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { getProductos } from '../services/productoService'
import { useCarritoStore } from '../stores/carrito'
import CartIcon from '../components/CartIcon.vue'
import axios from 'axios' // <-- IMPORTANTE: Necesitamos axios para llamar a la nueva API

const productos = ref([])
const busqueda = ref('')
const carrito = useCarritoStore()

// NUEVO: Variables para las Categorías (Práctica 06)
const categorias = ref([])
const categoriaActiva = ref(null)

// Variables para la paginación
const paginaActual = ref(1)
const itemsPorPagina = 10

onMounted(async () => {
  try {
    // 1. Descargamos las categorías para crear los botones
    const resCategorias = await axios.get('http://localhost:8000/api/categorias')
    categorias.value = resCategorias.data.data

    // 2. Descargamos todos los productos por defecto
    const res = await getProductos()
    productos.value = res.data.data
  } catch (error) {
    console.error("Error al cargar datos", error)
  }
})

// NUEVO: Función que se ejecuta al darle clic a una categoría
const filtrarPorCategoria = async (cat) => {
  try {
    categoriaActiva.value = cat
    // Llamamos a la ruta anidada que programaste en Laravel
    const { data } = await axios.get(`http://localhost:8000/api/categorias/${cat.id}/productos`)
    productos.value = data.data
    paginaActual.value = 1 // Reseteamos a la página 1
    busqueda.value = ''    // Limpiamos el buscador
  } catch (error) {
    console.error("Error al filtrar", error)
  }
}

// NUEVO: Función para quitar el filtro y volver a ver todos
const verTodos = async () => {
  try {
    categoriaActiva.value = null
    const res = await getProductos()
    productos.value = res.data.data
    paginaActual.value = 1
  } catch (error) {
    console.error("Error al cargar todos", error)
  }
}

// 1. Primero aplicamos el filtro de búsqueda
const productosFiltrados = computed(() =>
  productos.value.filter(p =>
    p.nombre.toLowerCase().includes(busqueda.value.toLowerCase())
  )
)

// 2. Calculamos cuántas páginas totales hay
const totalPaginas = computed(() =>
  Math.ceil(productosFiltrados.value.length / itemsPorPagina) || 1
)

// 3. Extraemos solo los 10 productos de la página actual
const productosPaginados = computed(() => {
  const inicio = (paginaActual.value - 1) * itemsPorPagina
  const fin = inicio + itemsPorPagina
  return productosFiltrados.value.slice(inicio, fin)
})

watch(busqueda, () => {
  paginaActual.value = 1
})

const cambiarPagina = (delta) => {
  const nueva = paginaActual.value + delta
  if (nueva >= 1 && nueva <= totalPaginas.value) {
    paginaActual.value = nueva
  }
}
</script>

<template>
  <div class="catalogo">
    <div class="cabecera-catalogo">
      <h2>Catálogo de Productos</h2>
      <CartIcon />
    </div>

    <div class="tabs-categorias">
      <button
        :class="{ activo: categoriaActiva === null }"
        @click="verTodos"
      >
        Todos
      </button>

      <button
        v-for="cat in categorias"
        :key="cat.id"
        :class="{ activo: categoriaActiva?.id === cat.id }"
        @click="filtrarPorCategoria(cat)"
      >
        {{ cat.nombre }}
      </button>
    </div>
    
    <input 
      v-model="busqueda" 
      type="text" 
      placeholder="Buscar por nombre..." 
      class="buscador"
    />

    <div class="grid">
      <div v-for="producto in productosPaginados" :key="producto.id" class="card">
        
        <img
          :src="producto.imagen_url || 'https://placehold.co/400x300/2a2a2a/ffffff?text=Sin+Imagen'"
          :alt="producto.nombre"
          class="producto-imagen"
          @error="e => e.target.src = 'https://placehold.co/400x300/2a2a2a/ffffff?text=Error'"
        />

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

      <div v-if="productosPaginados.length === 0" class="sin-resultados">
        No se encontraron productos.
      </div>
    </div>

    <div class="paginacion" v-if="totalPaginas > 1">
      <button @click="cambiarPagina(-1)" :disabled="paginaActual === 1">Anterior</button>
      <span>Página {{ paginaActual }} de {{ totalPaginas }}</span>
      <button @click="cambiarPagina(1)" :disabled="paginaActual === totalPaginas">Siguiente</button>
    </div>

  </div>
</template>

<style scoped>
.cabecera-catalogo { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.buscador { width: 100%; padding: 10px; margin-bottom: 20px; box-sizing: border-box; background: #2a2a2a; border: 1px solid #444; color: white; border-radius: 4px; }
.grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 30px;
  width: 100%;
}
@media (max-width: 900px) {
  .grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (max-width: 500px) {
  .grid {
    grid-template-columns: 1fr;
  }
}
.card {
  background: #1e1e1e;
  padding: 10px;
  border-radius: 8px;
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  width: 100%; /* La tarjeta llena su propia celda del grid */
  height: 250px;
  box-sizing: border-box; /* Evita que el padding rompa el ancho */
}
.producto-imagen {
  width: 100%;
  height: 200px;
  /* contain asegura que la foto completa sea visible, añadiendo barras laterales si es necesario,
     sin recortar nada (a diferencia de cover) */
  object-fit: cover; /*contain*/
  border-radius: 4px;
  background-color: #2a2a2a;
  margin-bottom: 1px;
}
.btn-detalle { display: inline-block; padding: 5px 10px; background: #555; color: #fff; text-decoration: none; border-radius: 4px; }
.btn-agregar { background: #38bdf8; color: #000; font-weight: bold; border: none; padding: 8px; cursor: pointer; border-radius: 4px; }
.btn-agregar:hover { background: #0284c7; }
.sin-resultados { width: 100%; text-align: center; padding: 20px; color: #aaa; }

/* Estilos de Paginación */
.paginacion { display: flex; justify-content: center; align-items: center; gap: 15px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #333; }
.paginacion button { background: #333; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; transition: 0.2s; }
.paginacion button:not(:disabled):hover { background: #38bdf8; color: black; }
.paginacion button:disabled { opacity: 0.4; cursor: not-allowed; }
.paginacion span { color: #ccc; font-weight: bold; }
.catalogo {
  width: 90%;
  margin: 0 auto;
  padding: 90px;
  box-sizing: border-box;
}

/* Estilos para las Categorías */
.tabs-categorias {
  display: flex;
  gap: 15px;
  margin-bottom: 25px;
  justify-content: center;
  flex-wrap: wrap; /* Por si en móviles no caben en una sola línea */
}

.tabs-categorias button {
  background: #1e1e1e;
  color: #ccc;
  border: 1px solid #444;
  padding: 8px 16px;
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.2s ease;
  font-weight: bold;
}

.tabs-categorias button:hover {
  background: #333;
  color: #fff;
}

.tabs-categorias button.activo {
  background: #38bdf8; /* Tu color azul característico */
  color: #000;
  border-color: #38bdf8;
}

</style>
