<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { createProducto } from '../../services/productoService'
import axios from 'axios' // <-- NUEVO: Necesitamos axios para traer las categorías

const router = useRouter()
// NUEVO: Agregamos categoria_id al estado inicial del formulario
const form = ref({ nombre: '', descripcion: '', precio: 0, stock: 0, categoria_id: '' })
const imagen = ref(null)      
const preview = ref(null)     
const mensaje = ref('')
const tipoMensaje = ref('exito')
const cargando = ref(false)

// NUEVO: Variable para almacenar las categorías y descargarlas al inicio
const categorias = ref([])

onMounted(async () => {
  try {
    const { data } = await axios.get('http://localhost:8000/api/categorias')
    categorias.value = data.data
  } catch (error) {
    console.error("Error al cargar categorías", error)
  }
})

const onImageChange = (e) => {
  const file = e.target.files[0]
  if (!file) return
  imagen.value = file
  preview.value = URL.createObjectURL(file) 
}

const mostrarMensaje = (texto, tipo = 'exito') => {
  mensaje.value = texto
  tipoMensaje.value = tipo
  setTimeout(() => {
    mensaje.value = ''
  }, 3000) 
}

const guardar = async () => {
  if (form.value.precio < 0) {
    return mostrarMensaje('El precio debe ser estrictamente mayor a 0.', 'error')
  }
  if (form.value.stock < 0) {
    return mostrarMensaje('El stock no puede ser un número negativo.', 'error')
  }
  if (!form.value.categoria_id) {
    return mostrarMensaje('Debes seleccionar una categoría.', 'error')
  }

  try {
    cargando.value = true 
    const fd = new FormData()
    fd.append('nombre', form.value.nombre)
    fd.append('descripcion', form.value.descripcion)
    fd.append('precio', form.value.precio)
    fd.append('stock', form.value.stock)
    
    // NUEVO: Agregamos el enlace simbólico (la categoría) al paquete
    fd.append('categoria_id', form.value.categoria_id)
    
    if (imagen.value) {
      fd.append('imagen', imagen.value)
    }

    await createProducto(fd)
    
    mensaje.value = 'Producto guardado exitosamente.'
    setTimeout(() => router.push('/admin/productos'), 1500)
  } catch (error) {
    console.error("Error al guardar", error)
    mensaje.value = 'Error al guardar el producto. Revisa la consola.'
  } finally {
    cargando.value = false 
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
      
      <div class="form-group">
        <label>Categoría del Sistema:</label>
        <select v-model="form.categoria_id" required>
          <option value="" disabled>Selecciona una categoría...</option>
          <option 
            v-for="cat in categorias" 
            :key="cat.id" 
            :value="cat.id"
          >
            {{ cat.nombre }}
          </option>
        </select>
      </div>
      
      <div class="file-upload">
        <label>Imagen de portada (Opcional):</label>
        <input type="file" accept="image/png, image/jpeg, image/webp" @change="onImageChange" />
        <div v-if="preview" class="preview-container">
          <img :src="preview" alt="Vista previa" />
        </div>
      </div>
      
      <button type="submit" class="btn-guardar" :disabled="cargando">
        {{ cargando ? 'Compilando...' : 'Guardar en Inventario' }}
      </button>
    </form>
  </div>
</template>

<style scoped>
.crear-box { background: #1e1e1e; padding: 20px; border-radius: 8px; max-width: 500px; margin: 0 auto;}
input[type="text"], input[type="number"], .file-upload { display: block; width: 100%; margin-bottom: 15px; padding: 10px; box-sizing: border-box; background: #2a2a2a; border: 1px solid #444; color: white; border-radius: 4px; }
.file-upload { background: #2a2a2a; border-radius: 4px; }
.file-upload label { display: block; margin-bottom: 10px; color: #ccc; }
.preview-container { margin-top: 10px; text-align: center; }
.preview-container img { max-width: 100%; max-height: 200px; border-radius: 4px; border: 1px solid #555; }
.btn-guardar { background: #38bdf8; color: #000; font-weight: bold; padding: 10px 20px; border: none; cursor: pointer; border-radius: 4px; width: 100%; }
.btn-guardar:disabled { background: #555; cursor: not-allowed; }
.alerta { color: #38bdf8; margin-bottom: 15px; text-align: center; font-weight: bold; }

/* NUEVO: Estilos para el selector y su contenedor */
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 8px; color: #ccc; font-weight: bold; }
select { 
  width: 100%; 
  padding: 10px; 
  background: #2a2a2a; 
  color: white; 
  border: 1px solid #444; 
  border-radius: 4px; 
  box-sizing: border-box;
  appearance: auto;
}
select:focus { outline: 1px solid #38bdf8; }
</style>

