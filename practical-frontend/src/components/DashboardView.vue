<script setup>
import ProductosList from './ProductosList.vue'
</script>

<template>
  <div>
    <h2 style="color: #0f0; margin-bottom: 10px;">Acceso Concedido - Panel de Control</h2>
    <p>Estás dentro del área segura autenticada por Sanctum.</p>
    <hr style="margin: 20px 0; border-color: #333;" />
    
    <ProductosList />
  </div>
</template>
