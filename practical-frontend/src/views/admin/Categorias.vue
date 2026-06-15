<script setup>
import { ref, onMounted } from 'vue'

// 1. IMPORTAMOS nuestras funciones del servicio (ruta relativa hacia arriba)
import {
  getCategorias,
  createCategoria,
  updateCategoria,
  deleteCategoria
} from '../../services/categoriaService'

const categorias = ref([])
const formNueva = ref({ nombre: '', descripcion: '' })
const formEdit = ref({ nombre: '', descripcion: '' })
const editando = ref(null)
const mensaje = ref('')
const tipoMensaje = ref('exito')

const mostrarMensaje = (texto, tipo = 'exito') => {
  mensaje.value = texto
  tipoMensaje.value = tipo
  setTimeout(() => { mensaje.value = '' }, 3000)
}

const cargarCategorias = async () => {
  try {
    // 2. USAMOS EL SERVICIO para obtener (GET)
    const res = await getCategorias()
    categorias.value = res.data.data || res.data
  } catch (error) {
    console.error("Error cargando categorías:", error)
  }
}

onMounted(cargarCategorias)

// --- CREAR ---
const guardarCategoria = async () => {
  try {
    // 3. USAMOS EL SERVICIO para crear (POST)
    await createCategoria(formNueva.value)
    mostrarMensaje('¡Categoría creada exitosamente!')
    formNueva.value.nombre = ''
    formNueva.value.descripcion = ''
    await cargarCategorias()
  } catch (error) {
    mostrarMensaje('Error al guardar la categoría.', 'error')
    console.error(error)
  }
}

// --- EDITAR ---
const iniciarEdicion = (cat) => {
  editando.value = cat.id
  formEdit.value = { ...cat }
}

const cancelarEdicion = () => {
  editando.value = null
}

const guardarEdicion = async (id) => {
  try {
    // 4. USAMOS EL SERVICIO para actualizar (PUT)
    await updateCategoria(id, formEdit.value)
    mostrarMensaje('¡Categoría actualizada correctamente!')
    editando.value = null
    await cargarCategorias()
  } catch (error) {
    mostrarMensaje('Error al actualizar la categoría.', 'error')
    console.error(error)
  }
}

// --- ELIMINAR ---
const eliminar = async (id) => {
  if (confirm('¿Seguro que deseas eliminar esta categoría permanentemente?')) {
    try {
      // 5. USAMOS EL SERVICIO para eliminar (DELETE)
      await deleteCategoria(id)
      mostrarMensaje('Categoría eliminada del sistema.')
      await cargarCategorias()
    } catch (error) {
      // El manejo del error 409 (Conflicto de BD) sigue funcionando idéntico
      if (error.response && error.response.status === 409) {
        mostrarMensaje(error.response.data.message, 'error')
      } else {
        mostrarMensaje('Error al intentar eliminar la categoría.', 'error')
      }
      console.error(error)
    }
  }
}
</script>

<template>
  <div>
    <h2 style="margin-bottom: 20px;">Gestión de Categorías</h2>

    <div v-if="mensaje" :class="['alerta', tipoMensaje === 'error' ? 'alerta-roja' : 'alerta-verde']">
      {{ mensaje }}
    </div>

    <div class="categoria-box">
      <h3>Añadir Nueva Categoría</h3>
      <form @submit.prevent="guardarCategoria">
        <div class="form-group">
          <label>Nombre de la Categoría</label>
          <input v-model="formNueva.nombre" type="text" placeholder="Ej. Frutas" required />
        </div>
        <div class="form-group">
          <label>Descripción de la Categoría (Opcional)</label>
          <input v-model="formNueva.descripcion" type="text" placeholder="Ej. Frutas frescas de temporada" />
        </div>
        <button type="submit" class="btn-guardar-nuevo">Guardar Categoría</button>
      </form>
    </div>

    <div v-if="categorias.length === 0" class="sin-datos">No hay categorías registradas.</div>
    
    <table v-else class="tabla-categorias">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="cat in categorias" :key="cat.id">
          
          <template v-if="editando !== cat.id">
            <td>{{ cat.id }}</td>
            <td>{{ cat.nombre }}</td>
            <td>{{ cat.descripcion || 'Sin descripción' }}</td>
            <td>
              <button class="btn-editar" @click="iniciarEdicion(cat)">Editar</button>
              <button class="btn-eliminar" @click="eliminar(cat.id)">Eliminar</button>
            </td>
          </template>

          <template v-else>
            <td>{{ cat.id }}</td>
            <td>
              <input v-model="formEdit.nombre" type="text" required class="input-edicion" />
            </td>
            <td>
              <input v-model="formEdit.descripcion" type="text" placeholder="Descripción..." class="input-edicion" />
            </td>
            <td>
              <button class="btn-guardar-nuevo" @click="guardarEdicion(cat.id)" style="margin-bottom: 5px;">Guardar</button>
              <button class="btn-cancelar" @click="cancelarEdicion">Cancelar</button>
            </td>
          </template>

        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
/* Estilos Base */
h2, h3 { color: white; margin-bottom: 15px; }

/* Caja de Creación */
.categoria-box { 
  background: #1e1e1e; 
  padding: 20px; 
  border-radius: 8px; 
  margin-bottom: 30px;
}
.form-group { margin-bottom: 15px; }
label { display: block; margin-bottom: 5px; color: #ccc; }
.categoria-box input { 
  display: block; 
  width: 100%; 
  padding: 10px; 
  box-sizing: border-box; 
  background: #2a2a2a; 
  border: 1px solid #444; 
  color: white; 
  border-radius: 4px; 
}
.categoria-box input:focus { outline: 1px solid #38bdf8; }

/* Botones */
.btn-guardar-nuevo { 
  background: #38bdf8; 
  color: black; 
  font-weight: bold; 
  padding: 10px 15px; 
  border: none; 
  cursor: pointer; 
  border-radius: 4px; 
}
.btn-guardar-nuevo:hover { background: #0284c7; }
.btn-editar { background-color: #38bdf8; color: #000; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; margin-right: 5px; }
.btn-eliminar, .btn-cancelar { background-color: #ff4444; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; }

/* Tabla */
.tabla-categorias { width: 100%; border-collapse: collapse; text-align: left; margin-top: 10px; }
.tabla-categorias th, .tabla-categorias td { padding: 12px; border-bottom: 1px solid #333; color: white; }
.tabla-categorias th { background-color: #1e1e1e; }
.sin-datos { padding: 20px; text-align: center; color: #aaa; background: #1e1e1e; border-radius: 5px; margin-top: 20px; }
.input-edicion { width: 90%; padding: 5px; background: #2a2a2a; color: white; border: 1px solid #555; border-radius: 3px; }

/* Alertas */
.alerta { margin-bottom: 15px; text-align: center; font-weight: bold; padding: 10px; border-radius: 4px; transition: 0.3s; }
.alerta-verde { color: #0f0; background: rgba(0, 255, 0, 0.1); border: 1px solid #0f0; }
.alerta-roja { color: #ff4444; background: rgba(255, 68, 68, 0.1); border: 1px solid #ff4444; }
</style>
