import { mount, flushPromises } from '@vue/test-utils'
import { vi, describe, it, expect, beforeEach } from 'vitest'
import axios from 'axios'
import ProductosList from '@/components/ProductosList.vue'
import { updateProducto, deleteProducto } from '@/services/productoService'

// 1. Secuestramos las dependencias externas
vi.mock('axios')
vi.mock('@/services/productoService', () => ({
  updateProducto: vi.fn(),
  deleteProducto: vi.fn()
}))

// 2. Secuestramos la ventana emergente de "confirm" del navegador para que siempre diga "Sí"
global.confirm = vi.fn(() => true)

describe('ProductosList.vue', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    // 3. Simulamos un backend respondiendo con 2 páginas para poder probar la paginación
    axios.get.mockImplementation((url) => {
      if (url.includes('/api/productos')) {
        return Promise.resolve({
          data: {
            data: [{ id: 1, nombre: 'Laptop ThinkPad', precio: 1200, stock: 5, categoria_id: 1 }],
            meta: { last_page: 2 } 
          }
        })
      }
      if (url.includes('/api/categorias')) {
        return Promise.resolve({ data: [{ id: 1, nombre: 'Laptops' }] })
      }
    })
  })

  it('permite cambiar de página usando la paginación', async () => {
    const wrapper = mount(ProductosList, {
      global: { directives: { can: () => {} } } // <-- Apagamos los warnings de v-can
    })
    await flushPromises()

    // Buscamos el botón "Siguiente" (el segundo botón de la paginación) y le damos clic
    const botones = wrapper.findAll('.paginacion-admin button')
    await botones[1].trigger('click')
    await flushPromises()

    // Verificamos que Axios haya intentado descargar la página 2
    expect(axios.get).toHaveBeenCalledWith('http://localhost:8000/api/productos?page=2')
  })

  it('permite abrir el modo edición y guardar cambios', async () => {
    const wrapper = mount(ProductosList, {
      global: { directives: { can: () => {} } }
    })
    await flushPromises()

    // Simulamos clic en Editar
    await wrapper.find('.btn-editar').trigger('click')
    await flushPromises()

    // Verificamos que la interfaz cambió y apareció el botón Guardar
    const btnGuardar = wrapper.find('.btn-guardar')
    expect(btnGuardar.exists()).toBe(true)

    // Simulamos que el servicio de actualización funciona y guardamos
    updateProducto.mockResolvedValueOnce(true)
    await btnGuardar.trigger('click')
    await flushPromises()

    // Verificamos que la alerta verde se muestre
    expect(updateProducto).toHaveBeenCalled()
    expect(wrapper.text()).toContain('¡Producto actualizado!')
  })

  it('permite eliminar un producto del inventario', async () => {
    const wrapper = mount(ProductosList, {
      global: { directives: { can: () => {} } }
    })
    await flushPromises()

    // Simulamos que el servicio de borrado funciona
    deleteProducto.mockResolvedValueOnce(true)
    
    // Disparamos el clic en el botón rojo
    await wrapper.find('.btn-eliminar').trigger('click')
    await flushPromises()

    // Verificamos que se haya mostrado el "confirm", que se haya llamado a la API y que salga la alerta
    expect(global.confirm).toHaveBeenCalled()
    expect(deleteProducto).toHaveBeenCalledWith(1)
    expect(wrapper.text()).toContain('Producto eliminado del sistema.')
  })
})
