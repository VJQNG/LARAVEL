<script setup>
import { defineAsyncComponent } from 'vue'
import Spinner from '../../components/Spinner.vue'

// Aplicamos defineAsyncComponent del manual
const GraficaVentas = defineAsyncComponent({
  // Simulamos una red lenta retrasando la importación 2 segundos
  loader: () => new Promise(resolve => {
    setTimeout(() => {
      resolve(import('../../components/GraficaVentas.vue'))
    }, 2000)
  }),
  loadingComponent: Spinner, // Mostramos el spinner mientras pasan los 2 segundos
  delay: 200,
  timeout: 5000
})
</script>

<template>
  <div class="dashboard-container">
    <h2>Bienvenido a la Tienda Arch</h2>
    <p>Panel de administración principal del sistema.</p>
    
    <GraficaVentas />
  </div>
</template>

<style scoped>
.dashboard-container { padding: 20px; color: white; }
</style>
