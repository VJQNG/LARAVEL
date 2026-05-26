<script setup>
import { ref, onMounted } from 'vue';
import { getProductos, updateProducto, deleteProducto } from '../services/productoService';

const productos = ref([]);
const editando = ref(null);
const formEdit = ref({ nombre: '', descripcion: '', precio: 0, stock: 0 });

const cargarProductos = async () => {
    try {
        const { data } = await getProductos();
        productos.value = data;
    } catch (error) {
        console.error("Error cargando productos", error);
    }
};

onMounted(cargarProductos);

// --- Lógica de Edición ---
const iniciarEdicion = (prod) => {
    editando.value = prod.id;
    formEdit.value = { ...prod };
};

const cancelarEdicion = () => {
    editando.value = null;
};

const guardarEdicion = async (id) => {
    try {
        await updateProducto(id, formEdit.value);
        editando.value = null;
        await cargarProductos();
    } catch (error) {
        console.error("Error al actualizar", error);
    }
};

// --- Lógica de Eliminación ---
const eliminar = async (id) => {
    if (confirm('¿Seguro que deseas eliminar este producto?')) {
        try {
            await deleteProducto(id);
            await cargarProductos();
        } catch (error) {
            console.error("Error al eliminar", error);
        }
    }
};
</script>

<template>
  <div class="lista-container">
    <div v-if="productos.length === 0" class="sin-datos">No hay productos en el inventario.</div>

    <table v-else class="tabla-productos">
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
        <tr v-for="prod in productos" :key="prod.id">
          <template v-if="editando !== prod.id">
            <td>{{ prod.id }}</td>
            <td>{{ prod.nombre }}</td>
            <td>${{ prod.precio }}</td>
            <td>{{ prod.stock }}</td>
            <td>
              <button class="btn-editar" @click="iniciarEdicion(prod)">Editar</button>
              <button class="btn-eliminar" @click="eliminar(prod.id)">Eliminar</button>
            </td>
          </template>

          <template v-else>
            <td>{{ prod.id }}</td>
            <td><input v-model="formEdit.nombre" /></td>
            <td><input v-model.number="formEdit.precio" type="number" step="0.01" /></td>
            <td><input v-model.number="formEdit.stock" type="number" /></td>
            <td>
              <button class="btn-guardar" @click="guardarEdicion(prod.id)">Guardar</button>
              <button class="btn-cancelar" @click="cancelarEdicion">Cancelar</button>
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
.sin-datos { padding: 20px; text-align: center; color: #aaa; background: #1e1e1e; border-radius: 5px; }
input { width: 90%; padding: 5px; }
</style>
