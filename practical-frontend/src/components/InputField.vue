<script setup>
// Definimos las propiedades que este componente puede recibir desde afuera
defineProps({
  label: String,
  modelValue: [String, Number],
  error: String,
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' }
})

// Definimos el evento para que v-model funcione correctamente en Vue 3
defineEmits(['update:modelValue'])
</script>

<template>
  <div class="form-group">
    <label>{{ label }}</label>
    
    <input 
      :type="type"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :placeholder="placeholder"
      :class="{ 'input-error': error }"
    />
    
    <span v-if="error" class="error-msg">{{ error }}</span>
  </div>
</template>

<style scoped>
.form-group { 
  margin-bottom: 20px; 
}
.form-group label { 
  display: block; 
  margin-bottom: 5px; 
  color: #ccc; 
  font-weight: bold;
}
.form-group input { 
  width: 100%; 
  padding: 10px; 
  box-sizing: border-box; 
  background: #2a2a2a; 
  border: 1px solid #444; 
  color: white; 
  border-radius: 4px; 
  transition: all 0.3s ease;
}
.form-group input:focus { 
  outline: none; 
  border-color: #38bdf8; 
}

/* Clases dinámicas para los errores */
.input-error { 
  border-color: #ff4444 !important; 
  background-color: rgba(255, 68, 68, 0.05) !important;
}
.error-msg { 
  color: #ff4444; 
  font-size: 0.85em; 
  margin-top: 5px; 
  display: block; 
}
</style>
