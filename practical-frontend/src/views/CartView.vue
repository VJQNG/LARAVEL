<script setup>
import { useCarritoStore } from '../stores/carrito'

const carrito = useCarritoStore()

const finalizarCompra = () => {
  if(confirm('¿Confirmas tu compra por $' + carrito.totalPrecio + '?')) {
    alert('Simulando conexión con pasarela de pago...')
    // Aquí implementaremos el Paso 3 opcional después
  }
}
</script>

<template>
  <div class="cart-view">
    <h2 style="color: #38bdf8;">Resumen de tu Carrito</h2>

    <div v-if="carrito.items.length === 0" class="vacio">
      <p>Tu carrito está completamente vacío.</p>
      <router-link to="/catalogo" class="btn-volver">Ir al catálogo</router-link>
    </div>

    <div v-else>
      <table class="tabla-carrito">
        <thead>
          <tr>
            <th>Imagen</th>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
            <th>Accion</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in carrito.items" :key="item.id">
            <td class="celda-centrada">
            <img
              :src="item.imagen_url || 'https://placehold.co/50x50/2a2a2a/ffffff?text=X'"
              :alt="item.nombre"
              class="miniatura-carrito"
              @error="e => e.target.src = 'https://placehold.co/50x50/2a2a2a/ffffff?text=Error'"
            />
            </td>
            <td>{{ item.nombre }}</td>
            <td>${{ item.precio }}</td>
            <td class="controles-cantidad">
              <button @click="carrito.cambiarCantidad(item.id, item.cantidad - 1)">-</button>
              <span>{{ item.cantidad }}</span>
              <button @click="carrito.cambiarCantidad(item.id, item.cantidad + 1)">+</button>
            </td>
            <td>${{ (item.precio * item.cantidad).toFixed(2) }}</td>
            <td>
              <button class="btn-quitar" @click="carrito.quitar(item.id)">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="resumen">
        <h3>Total General: <span class="total-verde">${{ carrito.totalPrecio.toFixed(2) }}</span></h3>
        <div class="botones-accion">
          <button class="btn-vaciar" @click="carrito.vaciar()">Vaciar Carrito</button>
          <button class="btn-finalizar" @click="finalizarCompra">Finalizar Compra</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cart-view { max-width: 800px; margin: 0 auto; padding: 20px; background: #1e1e1e; border-radius: 8px; margin-top: 20px;}
.vacio { text-align: center; padding: 40px; }
.btn-volver { display: inline-block; margin-top: 15px; padding: 10px 20px; background: #38bdf8; color: #000; text-decoration: none; border-radius: 5px; }
.tabla-carrito { width: 100%; border-collapse: collapse; margin-bottom: 20px; text-align: left; }
.tabla-carrito th, .tabla-carrito td { padding: 12px; border-bottom: 1px solid #333; }
.controles-cantidad { display: flex; align-items: center; gap: 10px; }
.controles-cantidad button { background: #333; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; }
.btn-quitar { background: #ff4444; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; }
.resumen { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 20px; border-top: 2px solid #333; }
.total-verde { color: #0f0; }
.botones-accion { display: flex; gap: 15px; }
.btn-vaciar { background: #555; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; }
.btn-finalizar { background: #0f0; color: #000; border: none; padding: 10px 15px; border-radius: 5px; font-weight: bold; cursor: pointer; }
.miniatura-carrito {
  width: 45px;
  height: 45px;
  object-fit: cover; /* Mantiene la proporción cuadrada perfecta */
  border-radius: 6px;
  border: 1px solid #444;
  background-color: #2a2a2a;
  display: block;
  margin: 0 auto; /* Centra la imagen dentro de su columna */
}
.celda-centrada {
  text-align: center;
  vertical-align: middle;
}
</style>
