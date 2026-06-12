import { mount } from '@vue/test-utils'
import InputField from '@/components/InputField.vue'

describe('InputField.vue', () => {
  // 1. Verificamos que dibuje correctamente las etiquetas (Labels)
  it('renderiza el label correctamente', () => {
    const wrapper = mount(InputField, { props: { label: 'Precio del Producto' } })
    expect(wrapper.text()).toContain('Precio del Producto')
  })

  // 2. Verificamos que reaccione visualmente cuando hay un error
  it('muestra el mensaje de error cuando existe', () => {
    const wrapper = mount(InputField, { props: { error: 'El stock no puede ser negativo' } })
    expect(wrapper.text()).toContain('El stock no puede ser negativo')
    expect(wrapper.find('.error-msg').exists()).toBe(true)
  })

  // 3. Simulamos interacción: ¿Avisa al componente padre cuando escribimos algo?
  it('emite el evento update:modelValue al escribir en el input', async () => {
    const wrapper = mount(InputField, { props: { modelValue: '' } })
    
    // Buscamos la etiqueta <input> y escribimos "Arch Linux"
    const input = wrapper.find('input')
    await input.setValue('Arch Linux')
    
    // Verificamos que el evento se haya disparado
    expect(wrapper.emitted('update:modelValue')).toBeTruthy()
    expect(wrapper.emitted('update:modelValue')[0]).toEqual(['Arch Linux'])
  })
})
