import { mount, flushPromises } from '@vue/test-utils'
import { vi, describe, it, expect, beforeEach } from 'vitest'
import RegisterView from '@/components/RegisterView.vue'

// 1. Secuestramos el Enrutador
const mockPush = vi.fn()
vi.mock('vue-router', () => ({
  useRouter: () => ({ push: mockPush })
}))

// 2. Secuestramos a Pinia (Auth Store)
const mockRegister = vi.fn()
vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({ register: mockRegister })
}))

describe('RegisterView.vue', () => {
  // Limpiamos la memoria antes de cada prueba
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('bloquea el envío si las contraseñas no coinciden', async () => {
    const wrapper = mount(RegisterView, {global: { stubs: ['router-link'] }})
    
    // Llenamos el formulario con errores intencionales
    await wrapper.find('input[type="text"]').setValue('Marcos')
    await wrapper.find('input[type="email"]').setValue('marcos@archlinux.org')
    const passwords = wrapper.findAll('input[type="password"]')
    await passwords[0].setValue('archlinux123')
    await passwords[1].setValue('diferente123') // <-- No coinciden

    await wrapper.find('form').trigger('submit.prevent')
    await flushPromises()

    // Verificamos que tu "if" atrape el error
    expect(wrapper.text()).toContain('Las contraseñas no coinciden.')
  })

  it('registra exitosamente y redirige al panel de admin', async () => {
    // Simulamos que el backend responde con éxito
    mockRegister.mockResolvedValueOnce(true) 

    const wrapper = mount(RegisterView, {global: { stubs: ['router-link'] }})
    await wrapper.find('input[type="text"]').setValue('Marcos')
    await wrapper.find('input[type="email"]').setValue('marcos@archlinux.org')
    const passwords = wrapper.findAll('input[type="password"]')
    await passwords[0].setValue('archlinux123')
    await passwords[1].setValue('archlinux123')

    await wrapper.find('form').trigger('submit.prevent')
    await flushPromises()

    // Verificamos que llamó a Pinia y luego al Router
    expect(mockRegister).toHaveBeenCalled()
    expect(mockPush).toHaveBeenCalledWith('/admin')
  })

  it('muestra un error específico si Laravel rechaza la validación (422)', async () => {
    // Simulamos que Laravel dice "Ese correo ya existe"
    mockRegister.mockRejectedValueOnce({
      response: {
        status: 422,
        data: { errors: { email: ['El correo electrónico ya está en uso.'] } }
      }
    })

    const wrapper = mount(RegisterView, {global: { stubs: ['router-link'] }})
    await wrapper.find('input[type="text"]').setValue('Marcos')
    await wrapper.find('input[type="email"]').setValue('usado@archlinux.org')
    const passwords = wrapper.findAll('input[type="password"]')
    await passwords[0].setValue('archlinux123')
    await passwords[1].setValue('archlinux123')

    await wrapper.find('form').trigger('submit.prevent')
    await flushPromises()

    // Verificamos que tu "catch" extraiga el error y lo pinte en pantalla
    expect(wrapper.text()).toContain('El correo electrónico ya está en uso.')
  })
})
