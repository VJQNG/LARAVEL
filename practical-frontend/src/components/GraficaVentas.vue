<template>
  <div class="grafica-fake">
    <h3>📊 Rendimiento de Ventas (Componente Lazy)</h3>
    <div class="barras">
      <div class="barra" style="height: 60%;"><span class="tooltip">Ene: $500</span></div>
      <div class="barra" style="height: 80%;"><span class="tooltip">Feb: $800</span></div>
      <div class="barra" style="height: 40%;"><span class="tooltip">Mar: $300</span></div>
      <div class="barra" style="height: 90%;"><span class="tooltip">Abr: $1000</span></div>
    </div>
  </div>
</template>

<style scoped>
.grafica-fake { background: #1e1e1e; padding: 20px; border-radius: 8px; border: 1px solid #333; margin-top: 20px;}
.grafica-fake h3 { color: white; margin-bottom: 20px; text-align: center;}
.barras { display: flex; justify-content: space-around; align-items: flex-end; height: 150px; border-bottom: 2px solid #555; padding-bottom: 5px;}
.barra { width: 40px; background: #38bdf8; border-radius: 4px 4px 0 0; transition: 0.3s; position: relative; cursor: pointer;}
.barra:hover { background: #0284c7; }
.tooltip { position: absolute; top: -25px; left: -10px; background: black; color: white; padding: 2px 5px; font-size: 12px; border-radius: 3px; opacity: 0;}
.barra:hover .tooltip { opacity: 1; }
</style>
