<script setup>
import { ref, onMounted } from 'vue';
import { getProductos, updateProducto, deleteProducto } from '../services/productoService';

const productos = ref([]);
const editando = ref(null);
const formEdit = ref({ nombre: '', descripcion: '', precio: 0, stock: 0 });
const imagenEdit = ref(null);

// Requisitos 2 y 3: Estados para la interfaz
const mensaje = ref('');
const tipoMensaje = ref('exito');
const cargando = ref(false);

const cargarProductos = async () => {
    try {
        const res = await getProductos();
        productos.value = res.data.data; 
    } catch (error) {
        console.error("Error cargando productos", error);
    }
};

onMounted(cargarProductos);

const mostrarMensaje = (texto, tipo = 'exito') => {
    mensaje.value = texto;
    tipoMensaje.value = tipo;
    setTimeout(() => {
        mensaje.value = '';
    }, 3000);
};

// --- Lógica de Edición ---
const iniciarEdicion = (prod) => {
    editando.value = prod.id;
    formEdit.value = { ...prod };
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
    // Requisito 1: Validación de negativos en la edición
    if (formEdit.value.precio <= 0) {
        return mostrarMensaje('Error: El precio debe ser mayor a $0.', 'error');
    }
    if (formEdit.value.stock < 0) {
        return mostrarMensaje('Error: El stock no puede ser negativo.', 'error');
    }

    try {
        cargando.value = true; // Encendemos spinner
        const fd = new FormData();
        fd.append('nombre', formEdit.value.nombre);
        fd.append('precio', formEdit.value.precio);
        fd.append('stock', formEdit.value.stock);
        
        if (formEdit.value.descripcion) {
            fd.append('descripcion', formEdit.value.descripcion);
        }

        if (imagenEdit.value) {
            fd.append('imagen', imagenEdit.value);
        }

        await updateProducto(id, fd);
        
        mostrarMensaje('¡Producto actualizado correctamente!', 'exito');
        editando.value = null;
        imagenEdit.value = null;
        await cargarProductos();
    } catch (error) {
        console.error("Error al actualizar", error);
        mostrarMensaje('Error al intentar actualizar el producto.', 'error');
    } finally {
        cargando.value = false; // Apagamos spinner
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

    <div v-if="productos.length === 0" class="sin-datos">No hay productos en el inventario.</div>

    <table v-else class="tabla-productos">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre y Foto</th>
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
              <button class="btn-editar" @click="iniciarEdicion(prod)" :disabled="cargando">Editar</button>
              <button class="btn-eliminar" @click="eliminar(prod.id)" :disabled="cargando">Eliminar</button>
            </td>
          </template>

          <template v-else>
            <td>{{ prod.id }}</td>
            <td>
              <input v-model="formEdit.nombre" required minlength="3" />
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
  </div>
</template>

<style scoped>
.lista-container { margin-top: 20px; }
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
.celda-nombre-foto {
  display: flex;
  align-items: center;
  gap: 15px; /* Separación entre la fotito y el texto */
}
.miniatura-tabla {
  width: 45px;
  height: 45px;
  object-fit: cover; /* Recorte cuadrado perfecto */
  border-radius: 6px; /* Bordes ligeramente redondeados */
  border: 1px solid #444; /* Un marquito sutil */
  background-color: #2a2a2a;
}
</style>
