import { mount } from '@vue/test-utils'
import { vi, describe, it, expect, beforeEach } from 'vitest'
import axios from 'axios'
import NuevoProducto from '@/views/admin/NuevoProducto.vue'

vi.mock('axios')

const mockPush = vi.fn()
vi.mock('vue-router', () => ({
  useRouter: () => ({ push: mockPush })
}))

describe('NuevoProducto.vue', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    
    // Disco duro estable
    const store = {}
    global.localStorage = {
      getItem: vi.fn((key) => key === 'token' ? 'fake-token' : (store[key] || null)),
      setItem: vi.fn((key, value) => { store[key] = value }),
      clear: vi.fn(() => { for (let k in store) delete store[k] })
    }

    global.URL.createObjectURL = vi.fn(() => 'blob:archlinux')
  })

  it('renderiza la vista y carga las categorias desde la API', async () => {
    axios.get.mockResolvedValueOnce({ data: { data: [{ id: 1, nombre: 'Tarjetas Gráficas' }] } })
    const wrapper = mount(NuevoProducto)
    
    // El equivalente a un "sleep 0.05" para dejar que Axios cargue
    await new Promise(r => setTimeout(r, 50)) 
    expect(wrapper.text()).toContain('Añadir Nuevo Producto')
  })

  it('procesa la subida de una imagen para la portada', async () => {
    axios.get.mockResolvedValueOnce({ data: [] })
    const wrapper = mount(NuevoProducto)
    await new Promise(r => setTimeout(r, 50))

    const file = new File(['(⌐□_□)'], 'foto.png', { type: 'image/png' })
    const inputFoto = wrapper.find('input[type="file"]')
    
    Object.defineProperty(inputFoto.element, 'files', { value: [file] })
    inputFoto.element.dispatchEvent(new Event('change'))
    
    await new Promise(r => setTimeout(r, 50))
    expect(global.URL.createObjectURL).toHaveBeenCalledWith(file)
  })

  it('envía el formulario exitosamente y redirige al panel', async () => {
    axios.get.mockResolvedValueOnce({ data: [{ id: 1, nombre: 'Laptops' }] })
    axios.post.mockResolvedValueOnce({ data: { message: 'Creado' } })

    const wrapper = mount(NuevoProducto)
    await new Promise(r => setTimeout(r, 50))

    // Atacamos el "hardware" directamente buscando las etiquetas <input> nativas
    const inputs = wrapper.findAll('input')
    await inputs[0].setValue('Laptop ThinkPad')
    await inputs[1].setValue('Ideal para compilar el Kernel')
    await inputs[2].setValue('1500')
    await inputs[3].setValue('10')
    
    await wrapper.find('select').setValue('1')

    // Disparamos el botón de guardado
    await wrapper.find('form').trigger('submit.prevent')
    
    // Le damos al CPU virtual 150ms para procesar el submit, Axios y actualizar la pantalla
    await new Promise(r => setTimeout(r, 150))

    expect(axios.post).toHaveBeenCalled()
    expect(wrapper.text()).toContain('Producto guardado exitosamente.')
  })

  it('atrapa y muestra errores 422 devueltos por el servidor', async () => {
    axios.get.mockResolvedValueOnce({ data: [{ id: 1, nombre: 'Laptops' }] })
    axios.post.mockRejectedValueOnce({
      response: { status: 422, data: { errors: { nombre: ['Ese producto ya existe en la base de datos.'] } } }
    })

    const wrapper = mount(NuevoProducto)
    await new Promise(r => setTimeout(r, 50))

    const inputs = wrapper.findAll('input')
    await inputs[0].setValue('Producto Duplicado')
    await inputs[1].setValue('Desc')
    await inputs[2].setValue('100')
    await inputs[3].setValue('5')
    
    await wrapper.find('select').setValue('1')

    await wrapper.find('form').trigger('submit.prevent')
    
    await new Promise(r => setTimeout(r, 150))

    expect(wrapper.text()).toContain('Corrige los errores del formulario.')
    expect(wrapper.text()).toContain('Ese producto ya existe en la base de datos.')
  })
})
