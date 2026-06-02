<script setup>
import { useCarritoStore } from '../stores/carrito'
const carrito = useCarritoStore()
</script>

<template>
  <router-link to="/carrito" class="cart-icon">
    🛒 Carrito
    <span v-if="carrito.totalItems > 0" class="badge">
      {{ carrito.totalItems }}
    </span>
  </router-link>
</template>

<style scoped>
.cart-icon { position: relative; text-decoration: none; color: #fff; background: #333; padding: 10px 15px; border-radius: 5px; font-weight: bold; }
.cart-icon:hover { background: #444; }
.badge { position: absolute; top: -8px; right: -8px; background: #ff4444; color: white; border-radius: 50%; padding: 2px 8px; font-size: 0.8em; font-weight: bold; }
</style>
