import { defineStore } from 'pinia'

export const useCarritoStore = defineStore('carrito', {
  // state: Lee del disco duro (localStorage) si ya había algo guardado
  state: () => ({
    items: JSON.parse(localStorage.getItem('carrito') || '[]')
  }),

  // getters: Cálculos matemáticos automáticos en tiempo real
  getters: {
    totalItems: (state) => state.items.reduce((suma, item) => suma + item.cantidad, 0),
    totalPrecio: (state) => state.items.reduce((suma, item) => suma + (item.precio * item.cantidad), 0),
    cantidadDeProducto: (state) => (id) => state.items.find(i => i.id === id)?.cantidad || 0
  },

  // actions: Los comandos para modificar el carrito
  actions: {
    agregar(producto) {
      const existe = this.items.find(i => i.id === producto.id)
      if (existe) {
        existe.cantidad++ // Si existe, suma 1
      } else {
        // Si no existe, lo inyecta con cantidad inicial 1
        this.items.push({ ...producto, cantidad: 1 })
      }
      this.guardarEnDisco()
    },

    quitar(id) {
      this.items = this.items.filter(i => i.id !== id)
      this.guardarEnDisco()
    },

    cambiarCantidad(id, cantidad) {
      if (cantidad <= 0) {
        this.quitar(id) // Si baja de 1, se destruye del carrito
        return
      }
      const item = this.items.find(i => i.id === id)
      if (item) {
        item.cantidad = cantidad
        this.guardarEnDisco()
      }
    },

    vaciar() {
      this.items = []
      this.guardarEnDisco()
    },

    // Función auxiliar para sincronizar con LocalStorage
    guardarEnDisco() {
      localStorage.setItem('carrito', JSON.stringify(this.items))
    }
  }
})
