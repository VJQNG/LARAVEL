<script setup>
import { ref, onMounted } from 'vue'
import { getProductos, createProducto, updateProducto, deleteProducto } from '../services/productoService'

// Estados reactivos (Nuestra RAM)
const productos = ref([])
const mensaje = ref('')
const modoEdicion = ref(false)

// Modelo del formulario
const form = ref({
  id: null,
  nombre: '',
  descripcion: '',
  precio: 0,
  stock: 0
})

// Cargar datos al iniciar
onMounted(async () => {
  await cargarLista()
})

const cargarLista = async () => {
  try {
    const respuesta = await getProductos()
    productos.value = respuesta.data
  } catch (error) {
    mensaje.value = 'Error al cargar los productos.'
  }
}

// Guardar o Actualizar
const guardar = async () => {
  try {
    if (modoEdicion.value) {
      await updateProducto(form.value.id, form.value)
      mensaje.value = 'Producto actualizado correctamente.'
    } else {
      await createProducto(form.value)
      mensaje.value = 'Producto creado correctamente.'
    }
    limpiarFormulario()
    await cargarLista()
  } catch (error) {
    mensaje.value = 'Hubo un error al guardar.'
  }
}

// Preparar formulario para editar
const editar = (producto) => {
  modoEdicion.value = true
  form.value = { ...producto }
  mensaje.value = ''
}

// Eliminar con confirmación
const eliminar = async (id) => {
  if (confirm('¿Estás seguro de eliminar este producto?')) {
    try {
      await deleteProducto(id)
      mensaje.value = 'Producto eliminado.'
      await cargarLista()
    } catch (error) {
      mensaje.value = 'Error al eliminar.'
    }
  }
}

const limpiarFormulario = () => {
  modoEdicion.value = false
  form.value = { id: null, nombre: '', descripcion: '', precio: 0, stock: 0 }
}
</script>

<template>
  <div class="container">
    <div v-if="mensaje" class="alerta">{{ mensaje }}</div>

    <div class="formulario">
      <h2>{{ modoEdicion ? 'Editar Producto' : 'Nuevo Producto' }}</h2>
      <form @submit.prevent="guardar">
        <input v-model="form.nombre" type="text" placeholder="Nombre del producto" required />
        <input v-model="form.descripcion" type="text" placeholder="Descripción" />
        <input v-model="form.precio" type="number" step="0.01" placeholder="Precio" required />
        <input v-model="form.stock" type="number" placeholder="Stock" required />
        
        <button type="submit">{{ modoEdicion ? 'Actualizar' : 'Crear' }}</button>
        <button type="button" v-if="modoEdicion" @click="limpiarFormulario">Cancelar</button>
      </form>
    </div>

    <div class="tabla-contenedor">
      <h2>Lista de Productos</h2>
      <table border="1" width="100%">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="producto in productos" :key="producto.id">
            <td>{{ producto.id }}</td>
            <td>{{ producto.nombre }}</td>
            <td>${{ producto.precio }}</td>
            <td>{{ producto.stock }}</td>
            <td>
              <button @click="editar(producto)">Editar</button>
              <button @click="eliminar(producto.id)" style="color: red;">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.alerta { background-color: #333; color: #0f0; padding: 10px; margin-bottom: 15px; }
.formulario { background: #1e1e1e; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
input { display: block; margin-bottom: 10px; padding: 8px; width: 90%; }
button { padding: 8px 15px; margin-right: 10px; cursor: pointer; }
table { border-collapse: collapse; margin-top: 10px; }
th, td { padding: 10px; text-align: left; }
</style>
