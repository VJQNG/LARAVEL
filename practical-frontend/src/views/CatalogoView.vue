<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useCarritoStore } from '../stores/carrito'
import CartIcon from '../components/CartIcon.vue'
import axios from 'axios'
import { useFiltros } from '../composables/useFiltros'

const carrito = useCarritoStore()
const route = useRoute()
const { filtros } = useFiltros()

const categorias = ref([])
const resultado = ref({ data: [], meta: {} })
const cargando = ref(false)

// 1. Descargamos las categorías leyendo el token dinámicamente
const cargarCategorias = async () => {
  try {
    const token = localStorage.getItem('token')
    if (!token) return
    const res = await axios.get('http://localhost:8000/api/categorias', {
      headers: { Authorization: `Bearer ${token}` }
    })
    categorias.value = res.data.data || res.data
  } catch (error) {
    console.error("Error al cargar categorías", error)
  }
}

// 2. Descargamos los productos filtrados
const cargarProductos = async () => {
  cargando.value = true
  try {
    const token = localStorage.getItem('token')
    if (!token) return
    // Filtro absoluto purificado
    const parametros = { page: filtros.pagina }
    if (filtros.busqueda) parametros.busqueda = filtros.busqueda
    if (filtros.categoria_id) parametros.categoria_id = filtros.categoria_id
    if (filtros.precio_min) parametros.precio_min = filtros.precio_min
    if (filtros.precio_max) parametros.precio_max = filtros.precio_max

    const { data } = await axios.get('http://localhost:8000/api/productos', {
      headers: { Authorization: `Bearer ${token}` },
      params: parametros
    })
    resultado.value = data
  } catch (error) {
    console.error("Error al filtrar en el backend", error)
  } finally {
    cargando.value = false
  }
}

onMounted(() => {
  cargarCategorias()
})

watch(() => route.query, cargarProductos, { immediate: true })

const filtrarPorCategoria = (catId) => {
  filtros.categoria_id = catId
  filtros.pagina = 1
}

const cambiarPagina = (delta) => {
  const nueva = filtros.pagina + delta
  if (resultado.value.meta && nueva >= 1 && nueva <= resultado.value.meta.last_page) {
    filtros.pagina = nueva
  }
}

watch(() => filtros.busqueda, () => {
  filtros.pagina = 1
})
</script>

<template>
  <div class="catalogo">
    <div class="cabecera-catalogo">
      <h2>Catálogo de Productos</h2>
      <div style="display: flex; align-items: center; gap: 15px;">
          <router-link to="/admin" class="btn-agregar" style="text-decoration: none;">
          Regresar al Panel
        </router-link>
        <CartIcon />
      </div>
    </div>

    <div class="tabs-categorias">
      <button
        :class="{ activo: !filtros.categoria_id }"
        @click="filtrarPorCategoria('')"
      >
        Todos
      </button>

      <button
        v-for="cat in categorias"
        :key="cat.id"
        :class="{ activo: Number(filtros.categoria_id) === cat.id }"
        @click="filtrarPorCategoria(cat.id)"
      >
        {{ cat.nombre }}
      </button>
    </div>

    <input
      v-model="filtros.busqueda"
      type="text"
      placeholder="Buscar por nombre..."
      class="buscador"
    />

    <div v-if="cargando" class="sin-resultados">
      ⏳ Descargando datos del servidor...
    </div>

    <div v-else class="grid">
      <div v-for="producto in resultado.data" :key="producto.id" class="card">

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

      <div v-if="resultado.data && resultado.data.length === 0" class="sin-resultados">
        No se encontraron productos.
      </div>
    </div>

    <div class="paginacion" v-if="resultado.meta && resultado.meta.last_page > 1">
      <button @click="cambiarPagina(-1)" :disabled="filtros.pagina === 1">Anterior</button>
      <span>Página {{ filtros.pagina }} de {{ resultado.meta.last_page }}</span>
      <button @click="cambiarPagina(1)" :disabled="filtros.pagina === resultado.meta.last_page">Siguiente</button>
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

