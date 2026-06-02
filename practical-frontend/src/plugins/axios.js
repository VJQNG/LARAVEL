import axios from 'axios'
import router from '../router' // Importaremos el router en el siguiente paso

// Configuración global
axios.defaults.baseURL = 'http://localhost:8000/api'
axios.defaults.headers.common['Accept'] = 'application/json' // Forzamos a que Laravel no nos redirija como humanos

// Interceptor de PETICIÓN (Antes de salir al servidor)
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    // Si tenemos llave, la pegamos en la cabecera
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Interceptor de RESPUESTA (Cuando el servidor nos contesta)
axios.interceptors.response.use(
  res => res,
  err => {
    // Si Laravel nos batea con un 401, mandamos al usuario a la pantalla de login de Vue
    if (err.response?.status === 401) {
      localStorage.removeItem('token') // Limpiamos llaves corruptas
      router.push('/login')
    }
    return Promise.reject(err)
  }
)

export default axios
