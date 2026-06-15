<template>
  <div class="spinner-container">
    <div class="spinner"></div>
    <p>Descargando módulo pesado...</p>
  </div>
</template>

<style scoped>
.spinner-container { text-align: center; padding: 20px; color: #38bdf8; }
.spinner {
  border: 4px solid rgba(255, 255, 255, 0.1);
  width: 40px; height: 40px; border-radius: 50%;
  border-left-color: #38bdf8;
  animation: spin 1s linear infinite;
  margin: 0 auto 10px auto;
}
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>
