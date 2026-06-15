<script setup>
import { ref, onMounted } from 'vue';
import { getProductos, updateProducto, deleteProducto } from '../services/productoService';
import { getCategorias } from '../services/categoriaService';
// import api from '../plugins/axios';
import { productoSchema } from '../schemas/productoSchema';

const productos = ref([]);
const editando = ref(null);
const formEdit = ref({ nombre: '', descripcion: '', precio: 0, stock: 0, categoria_id: '' });
const imagenEdit = ref(null);
const categorias = ref([]);
const metaInfo = ref(null);
const paginaActual = ref(1);

// Nuevos estados para los filtros de búsqueda
const busqueda = ref('');
const categoriaSeleccionada = ref('');

const mensaje = ref('');
const tipoMensaje = ref('exito');
const cargando = ref(false);

/*
// Función modificada para enviar filtros por URL (como el catálogo)
const cargarProductos = async (page = paginaActual.value) => {
    paginaActual.value = page;
    try {
        const res = await api.get(`/productos`, {
            params: {
                page: paginaActual.value,
                busqueda: busqueda.value,
                categoria_id: categoriaSeleccionada.value || null
            }
        });
        productos.value = res.data.data;
        metaInfo.value = res.data.meta;
    } catch (error) {
        console.error("Error cargando productos", error);
    }
};
*/


const cargarProductos = async (page = paginaActual.value) => {
    paginaActual.value = page;
    try {
        // En lugar de axios.get, usamos nuestra función del servicio
        const res = await getProductos({
            page: paginaActual.value,
            busqueda: busqueda.value,
            categoria_id: categoriaSeleccionada.value || null
        });
        productos.value = res.data.data;
        metaInfo.value = res.data.meta;
    } catch (error) {
        console.error("Error cargando productos", error);
    }
};


/*
const cargarCategorias = async () => {
    try {
        const res = await axios.get('/categorias');
        categorias.value = res.data.data || res.data;
    } catch (error) {
        console.error("Error cargando categorías", error);
    }
};
*/

const cargarCategorias = async () => {
    try {
        // En lugar de axios.get, usamos el servicio
        const res = await getCategorias();
        categorias.value = res.data.data || res.data;
    } catch (error) {
        console.error("Error cargando categorías", error);
    }
};


const cambiarPagina = async (delta) => {
    const nueva = paginaActual.value + delta;
    if (metaInfo.value && nueva >= 1 && nueva <= metaInfo.value.last_page) {
        await cargarProductos(nueva);
    }
};

onMounted(async () => {
    await cargarCategorias();
    await cargarProductos();
});

const mostrarMensaje = (texto, tipo = 'exito') => {
    mensaje.value = texto;
    tipoMensaje.value = tipo;
    setTimeout(() => mensaje.value = '', 3000);
};

// --- Lógica de Edición ---
const iniciarEdicion = (prod) => {
    editando.value = prod.id;
    formEdit.value = {
        ...prod,
        descripcion: prod.descripcion || ''
    };
    imagenEdit.value = null;
};

const cancelarEdicion = () => {
    editando.value = null;
    imagenEdit.value = null;
};

const onEditImageChange = (e) => {
    imagenEdit.value = e.target.files[0];
};

const guardarEdicion = async (id) => {
    try {
        cargando.value = true;
        await productoSchema.validate(formEdit.value, { abortEarly: false });

        const fd = new FormData();
        fd.append('nombre', formEdit.value.nombre);
        fd.append('descripcion', formEdit.value.descripcion || '');
        fd.append('precio', formEdit.value.precio);
        fd.append('stock', formEdit.value.stock);
        fd.append('categoria_id', formEdit.value.categoria_id);

        if (imagenEdit.value) {
            fd.append('imagen', imagenEdit.value);
        }

        await updateProducto(id, fd);

        mostrarMensaje('¡Producto actualizado!', 'exito');
        editando.value = null;
        await cargarProductos();
    } catch (error) {
        if (error.name === 'ValidationError') {
            mostrarMensaje(error.errors[0], 'error');
        } else {
            console.error("Error al actualizar", error);
            mostrarMensaje('Error al actualizar el producto.', 'error');
        }
    } finally {
        cargando.value = false;
    }
};

// --- Lógica de Eliminación ---
const eliminar = async (id) => {
    if (confirm('¿Seguro que deseas eliminar este producto permanentemente?')) {
        try {
            await deleteProducto(id);
            mostrarMensaje('Producto eliminado del sistema.', 'exito');
            await cargarProductos();
        } catch (error) {
            console.error("Error al eliminar", error);
            mostrarMensaje('Error al eliminar el producto.', 'error');
        }
    }
};
</script>

<template>
  <div class="lista-container">
    
    <div v-if="mensaje" :class="['alerta', tipoMensaje === 'error' ? 'alerta-roja' : 'alerta-verde']">
      {{ mensaje }}
    </div>

    <div class="herramientas-inventario">
      <div class="filtros-categorias">
        <button :class="{ activo: categoriaSeleccionada === '' }" @click="categoriaSeleccionada = ''; cargarProductos(1)">Todos</button>
        <button v-for="cat in categorias" :key="cat.id" :class="{ activo: categoriaSeleccionada === cat.id }" @click="categoriaSeleccionada = cat.id; cargarProductos(1)">
          {{ cat.nombre }}
        </button>
      </div>
      <input type="text" v-model="busqueda" placeholder="Buscar por ID, nombre, precio o stock..." @input="cargarProductos(1)" class="buscador-admin" />
    </div>

    <div v-if="productos.length === 0" class="sin-datos">No se encontraron productos en el inventario.</div>

    <table v-else class="tabla-productos">
      <thead>
        <tr>
          <th>ID</th>
          <th>Foto y Nombre</th>
          <th>Precio</th>
          <th>Stock</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="prod in productos" :key="prod.id">
          <template v-if="editando !== prod.id">
            <td>{{ prod.id }}</td>
            <td class="celda-nombre-foto">
              <img
                :src="prod.imagen_url || 'https://placehold.co/50x50/2a2a2a/ffffff?text=X'"
                :alt="prod.nombre"
                class="miniatura-tabla"
                @error="e => e.target.src = 'https://placehold.co/50x50/2a2a2a/ffffff?text=Error'"
              />
              <span>{{ prod.nombre }}</span>
            </td>
            <td>${{ prod.precio }}</td>
            <td>{{ prod.stock }}</td>
            <td>
              <button class="btn-editar" v-can="'editar'" @click="iniciarEdicion(prod)" :disabled="cargando">Editar</button>
              <button class="btn-eliminar" v-can="'eliminar'" @click="eliminar(prod.id)" :disabled="cargando">Eliminar</button>
            </td>
          </template>

          <template v-else>
            <td>{{ prod.id }}</td>
            <td>
            <div class="campos-edicion">
              <input v-model="formEdit.nombre" required minlength="3" />
              <input v-model="formEdit.descripcion" placeholder="Descripción (Opcional)" class="input-edicion" />
              <select v-model="formEdit.categoria_id" required class="select-edicion">
                <option value="" disabled>Seleccionar Categoría</option>
                <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
                  {{ cat.nombre }}
                </option>
              </select>
            </div>
              <div class="edit-file-box">
                <small>Cambiar foto:</small>
                <input type="file" @change="onEditImageChange" accept="image/png, image/jpeg, image/webp" />
              </div>
            </td>
            <td><input v-model.number="formEdit.precio" type="number" step="0.01" min="0.1" required /></td>
            <td><input v-model.number="formEdit.stock" type="number" min="0" required /></td>
            <td>
              <button class="btn-guardar" @click="guardarEdicion(prod.id)" :disabled="cargando">
                {{ cargando ? '⏳...' : 'Guardar' }}
              </button>
              <button class="btn-cancelar" @click="cancelarEdicion" :disabled="cargando">Cancelar</button>
            </td>
          </template>
        </tr>
      </tbody>
    </table>

    <div class="paginacion-admin" v-if="metaInfo && metaInfo.last_page > 1">
      <button @click="cambiarPagina(-1)" :disabled="paginaActual === 1">Anterior</button>
      <span>Página {{ paginaActual }} de {{ metaInfo.last_page }}</span>
      <button @click="cambiarPagina(1)" :disabled="paginaActual === metaInfo.last_page">Siguiente</button>
    </div>
  </div>
</template>

<style scoped>
.lista-container { margin-top: 20px; }

/* Nuevos estilos para los filtros de búsqueda */
.herramientas-inventario { margin-bottom: 25px; background: #1e1e1e; padding: 20px; border-radius: 8px; border: 1px solid #333; }
.filtros-categorias { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 15px; justify-content: center; }
.filtros-categorias button { background: #2a2a2a; color: #ccc; border: 1px solid #444; padding: 8px 18px; border-radius: 20px; cursor: pointer; transition: 0.3s; font-weight: 500;}
.filtros-categorias button:hover { background: #333; }
.filtros-categorias button.activo { background: #38bdf8; color: #000; font-weight: bold; border-color: #38bdf8; }
.buscador-admin { width: 100%; padding: 12px 15px; border-radius: 6px; border: 1px solid #555; background: #2a2a2a; color: white; box-sizing: border-box; font-size: 15px;}
.buscador-admin:focus { outline: none; border-color: #38bdf8; box-shadow: 0 0 5px rgba(56, 189, 248, 0.3);}

/* Estilos de tabla existentes */
.tabla-productos { width: 100%; border-collapse: collapse; text-align: left; }
.tabla-productos th, .tabla-productos td { padding: 12px; border-bottom: 1px solid #333; }
.tabla-productos th { background-color: #1e1e1e; }
.btn-editar, .btn-guardar { background-color: #38bdf8; color: #000; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; margin-right: 5px; }
.btn-eliminar, .btn-cancelar { background-color: #ff4444; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; }
.btn-guardar:disabled, .btn-editar:disabled, .btn-eliminar:disabled, .btn-cancelar:disabled { opacity: 0.5; cursor: not-allowed; }
.sin-datos { padding: 20px; text-align: center; color: #aaa; background: #1e1e1e; border-radius: 5px; }
input[type="text"], input[type="number"] { width: 90%; padding: 5px; background: #2a2a2a; color: white; border: 1px solid #555; border-radius: 3px;}
.edit-file-box { margin-top: 8px; background: #222; padding: 5px; border-radius: 3px; }
.edit-file-box small { color: #888; display: block; margin-bottom: 3px; }
.edit-file-box input[type="file"] { font-size: 0.8em; width: 100%; color: #ccc; }
.alerta { margin-bottom: 15px; text-align: center; font-weight: bold; padding: 10px; border-radius: 4px; transition: 0.3s; }
.alerta-verde { color: #0f0; background: rgba(0, 255, 0, 0.1); border: 1px solid #0f0; }
.alerta-roja { color: #ff4444; background: rgba(255, 68, 68, 0.1); border: 1px solid #ff4444; }
.celda-nombre-foto { display: flex; align-items: center; gap: 15px; }
.miniatura-tabla { width: 45px; height: 45px; object-fit: cover; border-radius: 6px; border: 1px solid #444; background-color: #2a2a2a; }
.campos-edicion { display: flex; flex-direction: column; gap: 8px; margin-bottom: 10px; }
.campos-edicion input, .campos-edicion select { width: 100%; padding: 8px; background-color: #2a2a2a; color: white; border: 1px solid #555; border-radius: 4px; box-sizing: border-box; }
.campos-edicion input::placeholder { color: #888; }
.paginacion-admin { display: flex; justify-content: center; align-items: center; gap: 15px; margin-top: 20px; padding: 15px; background: #1e1e1e; border-radius: 5px; }
.paginacion-admin button { background: #333; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; transition: 0.2s; }
.paginacion-admin button:not(:disabled):hover { background: #38bdf8; color: black; }
.paginacion-admin button:disabled { opacity: 0.4; cursor: not-allowed; }
.paginacion-admin span { color: #ccc; font-weight: bold; }
</style>
