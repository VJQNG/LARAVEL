<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
// Importamos la conexión que acabamos de configurar
import echo from '../plugins/echo'

const pedidosNuevos = ref([])
const alertasStock = ref([])

onMounted(() => {
  // Nos suscribimos al túnel privado
  echo.private('admin-panel')
    .listen('NuevoPedidoRecibido', (e) => {
      // Cuando entra un pedido, lo agregamos al inicio de la lista
      pedidosNuevos.value.unshift(e)
      // Auto-eliminar la notificación de la pantalla después de 10 segundos
      setTimeout(() => pedidosNuevos.value.pop(), 10000)
    })
    .listen('StockBajoAlerta', (e) => {
      // Cuando el stock es bajo, lo registramos
      alertasStock.value.unshift(e)
    })
})

// Regla de oro de la RAM: Destruir la conexión WebSocket si salimos del panel
onUnmounted(() => {
  echo.leave('admin-panel')
})
</script>

<template>
  <div class="contenedor-notificaciones">
    <TransitionGroup name="toast">
      <div v-for="p in pedidosNuevos" :key="p.id" class="toast toast-pedido">
        <strong style="color: #38bdf8;">🛒 ¡Nuevo Pedido #{{ p.id }}!</strong><br>
        Cliente: {{ p.cliente }}<br>
        Monto: <strong>${{ p.total }}</strong>
      </div>
    </TransitionGroup>

    <TransitionGroup name="toast">
      <div v-for="a in alertasStock" :key="a.producto_id + '-' + a.stock_actual" class="toast toast-alerta">
        <strong style="color: #ff4444;">⚠️ Alerta de Stock</strong><br>
        {{ a.nombre }} se está agotando.<br>
        Quedan: <strong>{{ a.stock_actual }} unidades</strong>
      </div>
    </TransitionGroup>
  </div>
</template>

<style scoped>
.contenedor-notificaciones {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 10px;
  pointer-events: none; /* Para no bloquear clics debajo */
}

.toast {
  background: #1e1e1e;
  color: #ccc;
  padding: 15px 20px;
  border-radius: 8px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.5);
  border-left: 5px solid #333;
  width: 250px;
  font-size: 14px;
}

.toast-pedido { border-left-color: #38bdf8; }
.toast-alerta { border-left-color: #ff4444; }

/* Animaciones de entrada y salida */
.toast-enter-active, .toast-leave-active { transition: all 0.5s ease; }
.toast-enter-from { opacity: 0; transform: translateX(100%); }
.toast-leave-to { opacity: 0; transform: translateY(-30px); }
</style>
