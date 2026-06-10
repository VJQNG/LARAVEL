import { reactive, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'

export function useFiltros() {
    const route = useRoute()
    const router = useRouter()

    // Inicializamos el estado leyendo la URL (por si el usuario entra desde un link compartido)
    const filtros = reactive({
        busqueda: route.query.busqueda || '',
        categoria_id: route.query.categoria || '',
        precio_min: route.query.min || '',
        precio_max: route.query.max || '',
        pagina: Number(route.query.p) || 1,
    })

    // El "daemon": Observa los filtros y empuja los cambios a la URL
    watch(filtros, () => {
        router.push({ 
            query: {
                busqueda: filtros.busqueda || undefined,
                categoria: filtros.categoria_id || undefined,
                min: filtros.precio_min || undefined,
                max: filtros.precio_max || undefined,
                // Limpiamos la URL quitando "p=1" si estamos en la primera página
                p: filtros.pagina > 1 ? filtros.pagina : undefined,
            }
        })
    })

    return { filtros }
}
