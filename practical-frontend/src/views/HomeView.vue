<script setup>
import { ref, onMounted } from 'vue'
import { getProductos } from '../services/productoService'

const ultimosProductos = ref([])

onMounted(async () => {
  try {
    const res = await getProductos()
    // Tomamos los últimos 3 productos
    ultimosProductos.value = res.data.slice(-3).reverse()
  } catch (error) {
    console.error("Error al cargar productos del home", error)
  }
})
</script>

<template>
  <div class="home">
    <section class="hero">
      <h1>Bienvenido a la Tienda Arch</h1>
      <p>Hardware certificado para tu distribución favorita.</p>
      <router-link to="/catalogo" class="btn">Ver Catálogo Completo</router-link>
    </section>

    <h2>Últimas Novedades</h2>
    <div class="grid">
      <div v-for="producto in ultimosProductos" :key="producto.id" class="card">
        <h3>{{ producto.nombre }}</h3>
        <p>${{ producto.precio }}</p>
        <router-link :to="`/catalogo/${producto.id}`">Ver Detalle</router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.hero { text-align: center; padding: 40px; background: #1e1e1e; border-radius: 8px; margin-bottom: 20px; }
.btn { display: inline-block; margin-top: 10px; padding: 10px 20px; background: #38bdf8; color: #000; text-decoration: none; font-weight: bold; border-radius: 4px; }
.grid { display: flex; gap: 20px; justify-content: center; }
.card { background: #1e1e1e; padding: 15px; border-radius: 8px; width: 200px; text-align: center; }
a { color: #38bdf8; }
</style>
