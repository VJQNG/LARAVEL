<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { createProducto } from '../../services/productoService'

const router = useRouter()
const form = ref({ nombre: '', descripcion: '', precio: 0, stock: 0 })
const imagen = ref(null)      // Guardará el archivo binario del disco
const preview = ref(null)     // Guardará la URL temporal para ver la foto antes de subirla
const mensaje = ref('')
const tipoMensaje = ref('exito')
const cargando = ref(false)

// Se ejecuta cada vez que el usuario selecciona un archivo (Paso 4.5)
const onImageChange = (e) => {
  const file = e.target.files[0]
  if (!file) return
  imagen.value = file
  preview.value = URL.createObjectURL(file) // Magia de JS para previsualizar
}

const mostrarMensaje = (texto, tipo = 'exito') => {
  mensaje.value = texto
  tipoMensaje.value = tipo
  setTimeout(() => {
    mensaje.value = ''
  }, 3000) // Se destruye a los 3 segundos
}

const guardar = async () => {
  if (form.value.precio < 0) {
    return mostrarMensaje('El precio debe ser estrictamente mayor a 0.', 'error')
  }
  if (form.value.stock < 0) {
    return mostrarMensaje('El stock no puede ser un número negativo.', 'error')
  }
  try {
    // Empaquetamos todo en un FormData (como un .tar) porque JSON no soporta binarios (Paso 4.5)
    cargando.value = true // Encendemos el spinner
    const fd = new FormData()
    fd.append('nombre', form.value.nombre)
    fd.append('descripcion', form.value.descripcion)
    fd.append('precio', form.value.precio)
    fd.append('stock', form.value.stock)
    
    // Solo agregamos la imagen al paquete si el usuario seleccionó una
    if (imagen.value) {
      fd.append('imagen', imagen.value)
    }

    // Axios es lo suficientemente inteligente para detectar que esto es un FormData
    // y cambiará automáticamente los Headers a 'multipart/form-data'
    await createProducto(fd)
    
    mensaje.value = 'Producto guardado exitosamente.'
    setTimeout(() => router.push('/admin/productos'), 1500)
  } catch (error) {
    console.error("Error al guardar", error)
    mensaje.value = 'Error al guardar el producto. Revisa la consola.'
  } finally {
    cargando.value = false // Apagamos el spinner sin importar si falló o tuvo éxito
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
      
      <div class="file-upload">
        <label>Imagen de portada (Opcional):</label>
        <input type="file" accept="image/png, image/jpeg, image/webp" @change="onImageChange" />
        <div v-if="preview" class="preview-container">
          <img :src="preview" alt="Vista previa" />
        </div>
      </div>
      
      <button type="submit" class="btn-guardar">Guardar en Inventario</button>
    </form>
  </div>
</template>

<style scoped>
.crear-box { background: #1e1e1e; padding: 20px; border-radius: 8px; max-width: 500px; }
input[type="text"], input[type="number"], .file-upload { display: block; width: 100%; margin-bottom: 15px; padding: 10px; box-sizing: border-box; }
.file-upload { background: #2a2a2a; border-radius: 4px; }
.file-upload label { display: block; margin-bottom: 10px; color: #ccc; }
.preview-container { margin-top: 10px; text-align: center; }
.preview-container img { max-width: 100%; max-height: 200px; border-radius: 4px; border: 1px solid #555; }
.btn-guardar { background: #0f0; color: #000; font-weight: bold; padding: 10px 20px; border: none; cursor: pointer; border-radius: 4px; width: 100%; }
.alerta { color: #38bdf8; margin-bottom: 15px; text-align: center; font-weight: bold; }
</style>
