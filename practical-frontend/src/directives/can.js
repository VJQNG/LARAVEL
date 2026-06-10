import { watch } from 'vue'
import { useAuthStore } from '../stores/auth'

export const vCan = {
  mounted(el, binding) {
    const auth = useAuthStore()

    // Función aislada que aplica el CSS según el permiso
    const aplicarPermiso = () => {
      // Verificamos que 'permisos' exista y que el valor solicitado sea true
      if (auth.permisos && auth.permisos[binding.value] === true) {
        el.style.display = '' // Restaura el botón
      } else {
        el.style.display = 'none' // Oculta el botón
      }
    }

    // 1. Ejecutamos el chequeo al cargar la tabla por primera vez
    aplicarPermiso()

    // 2. El Demonio: Vigila la memoria global de Pinia sin importar el componente
    watch(
      () => auth.permisos, 
      () => {
        aplicarPermiso()
      }, 
      { deep: true } // Escucha cambios profundos dentro del objeto
    )
  }
}
