<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
// Importaciones del nuevo motor de validación
import { useForm, useField } from 'vee-validate'
import { productoSchema } from '../../schemas/productoSchema'
import InputField from '../../components/InputField.vue'

const router = useRouter()
const categorias = ref([])
const imagen = ref(null)      
const preview = ref(null)     
const mensaje = ref('')
const tipoMensaje = ref('exito')
const cargando = ref(false)

// 1. Inicializamos VeeValidate pasándole tu archivo de reglas (Schema)
const { handleSubmit, errors, resetForm } = useForm({
  validationSchema: productoSchema
})

// 2. Extraemos los campos. Vue/VeeValidate los mantendrá sincronizados automáticamente.
const { value: nombre } = useField('nombre')
const { value: descripcion } = useField('descripcion')
const { value: precio } = useField('precio')
const { value: stock } = useField('stock')
const { value: categoria_id } = useField('categoria_id')

// 3. Este objeto atrapará los errores 422 si alguien intenta burlar al frontend
const erroresServidor = ref({})

onMounted(async () => {
  try {
    const token = localStorage.getItem('token')
    const { data } = await axios.get('http://localhost:8000/api/categorias', {
      headers: { Authorization: `Bearer ${token}` }
    })
    categorias.value = data.data || data
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

// 4. handleSubmit verifica automáticamente que no haya errores de Yup antes de ejecutar esto
const onSubmit = handleSubmit(async (values) => {
  try {
    cargando.value = true 
    erroresServidor.value = {} // Limpiamos errores previos
    
    const fd = new FormData()
    fd.append('nombre', values.nombre)
    if (values.descripcion) fd.append('descripcion', values.descripcion)
    fd.append('precio', values.precio ? values.precio : 0)
    fd.append('stock', values.stock ? values.stock : 0)
    fd.append('categoria_id', values.categoria_id)
    
    if (imagen.value) {
      fd.append('imagen', imagen.value)
    }

    const token = localStorage.getItem('token')
    await axios.post('http://localhost:8000/api/productos', fd, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'multipart/form-data'
      }
    })
    
    mensaje.value = 'Producto guardado exitosamente.'
    tipoMensaje.value = 'exito'
    setTimeout(() => router.push('/admin/productos'), 1500)
    
  } catch (error) {
    console.error("Error al guardar", error)
    // Si Laravel detecta algo raro y devuelve 422, atrapamos los mensajes
    if (error.response?.status === 422) {
      erroresServidor.value = error.response.data.errors
      mensaje.value = 'Corrige los errores del formulario.'
    } else {
      mensaje.value = 'Error al guardar el producto. Revisa la consola.'
    }
    tipoMensaje.value = 'error'
  } finally {
    cargando.value = false 
  }
})
</script>

<template>
  <div class="crear-box">
    <h2>Añadir Nuevo Producto</h2>
    
    <div v-if="mensaje" :class="['alerta', tipoMensaje === 'error' ? 'alerta-roja' : 'alerta-verde']">
      {{ mensaje }}
    </div>
    
    <form @submit.prevent="onSubmit">
      
      <InputField
        label="Nombre del producto"
        v-model="nombre"
        :error="errors.nombre || erroresServidor.nombre?.[0]"
      />

      <InputField
        label="Descripción (Opcional)"
        v-model="descripcion"
        :error="errors.descripcion || erroresServidor.descripcion?.[0]"
      />

      <InputField
        label="Precio"
        type="number"
        v-model="precio"
        :error="errors.precio || erroresServidor.precio?.[0]"
      />

      <InputField
        label="Stock inicial"
        type="number"
        v-model="stock"
        :error="errors.stock || erroresServidor.stock?.[0]"
      />
      
      <div class="form-group">
        <label>Categoría del Sistema:</label>
        <select 
          v-model="categoria_id" 
          :class="{ 'input-error': errors.categoria_id || erroresServidor.categoria_id }"
        >
          <option value="" disabled>Selecciona una categoría...</option>
          <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
            {{ cat.nombre }}
          </option>
        </select>
        <span v-if="errors.categoria_id" class="error-msg">{{ errors.categoria_id }}</span>
        <span v-if="erroresServidor.categoria_id" class="error-msg">{{ erroresServidor.categoria_id[0] }}</span>
      </div>
      
      <div class="file-upload">
        <label>Imagen de portada (Opcional):</label>
        <input type="file" accept="image/png, image/jpeg, image/webp" @change="onImageChange" />
        <span v-if="erroresServidor.imagen" class="error-msg">{{ erroresServidor.imagen[0] }}</span>
        
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
.file-upload { background: #2a2a2a; border-radius: 4px; padding: 10px; margin-bottom: 15px; border: 1px solid #444; }
.file-upload label { display: block; margin-bottom: 10px; color: #ccc; font-weight: bold; }
.preview-container { margin-top: 10px; text-align: center; }
.preview-container img { max-width: 100%; max-height: 200px; border-radius: 4px; border: 1px solid #555; }
.btn-guardar { background: #38bdf8; color: #000; font-weight: bold; padding: 10px 20px; border: none; cursor: pointer; border-radius: 4px; width: 100%; transition: 0.2s;}
.btn-guardar:disabled { background: #555; cursor: not-allowed; }

.alerta { margin-bottom: 15px; text-align: center; font-weight: bold; padding: 10px; border-radius: 4px; transition: 0.3s; }
.alerta-verde { color: #0f0; background: rgba(0, 255, 0, 0.1); border: 1px solid #0f0; }
.alerta-roja { color: #ff4444; background: rgba(255, 68, 68, 0.1); border: 1px solid #ff4444; }

.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 5px; color: #ccc; font-weight: bold; }
select { 
  width: 100%; 
  padding: 10px; 
  background: #2a2a2a; 
  color: white; 
  border: 1px solid #444; 
  border-radius: 4px; 
  box-sizing: border-box;
  appearance: auto;
  transition: all 0.3s ease;
}
select:focus { outline: none; border-color: #38bdf8; }

.input-error { border-color: #ff4444 !important; background-color: rgba(255, 68, 68, 0.05) !important; }
.error-msg { color: #ff4444; font-size: 0.85em; margin-top: 5px; display: block; }
</style>
