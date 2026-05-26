<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { createProducto } from '../../services/productoService'

const router = useRouter()
const form = ref({ nombre: '', descripcion: '', precio: 0, stock: 0 })
const mensaje = ref('')

const guardar = async () => {
  try {
    await createProducto(form.value)
    mensaje.value = 'Producto guardado exitosamente.'
    // Después de guardar, mandamos al usuario de regreso a ver la tabla
    setTimeout(() => router.push('/admin/productos'), 1500)
  } catch (error) {
    console.error("Error al guardar", error)
    mensaje.value = 'Error al guardar el producto.'
  }
}
</script>

<template>
  <div class="crear-box">
    <h2>Añadir Nuevo Producto</h2>
    <div v-if="mensaje" class="alerta">{{ mensaje }}</div>
    
    <form @submit.prevent="guardar">
      <input v-model="form.nombre" placeholder="Nombre del producto" required />
      <input v-model="form.descripcion" placeholder="Descripción" />
      <input v-model.number="form.precio" type="number" step="0.01" placeholder="Precio" required />
      <input v-model.number="form.stock" type="number" placeholder="Stock inicial" required />
      
      <button type="submit" class="btn-guardar">Guardar en Inventario</button>
    </form>
  </div>
</template>

<style scoped>
.crear-box { background: #1e1e1e; padding: 20px; border-radius: 8px; max-width: 500px; }
input { display: block; width: 100%; margin-bottom: 15px; padding: 10px; box-sizing: border-box; }
.btn-guardar { background: #0f0; color: #000; font-weight: bold; padding: 10px 20px; border: none; cursor: pointer; border-radius: 4px; }
.alerta { color: #38bdf8; margin-bottom: 15px; }
</style>
