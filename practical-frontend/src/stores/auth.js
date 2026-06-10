import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  // state: Nuestra memoria RAM
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null, // Recupera la llave si ya existía en el disco
    permisos: { crear: false, editar: false, eliminar: false }
  }),
  
  // getters: Consultas rápidas al estado
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  
  // actions: Los comandos para alterar la memoria
  actions: {
    async login(credentials) {
      const res = await axios.post('http://localhost:8000/api/login', credentials)
      this.token = res.data.token
      this.user = res.data.user
      localStorage.setItem('token', this.token) // Guardamos la llave en disco
    },
    
    async register(data) {
      const res = await axios.post('http://localhost:8000/api/register', data)
      this.token = res.data.token
      this.user = res.data.user
      localStorage.setItem('token', this.token)
    },

    async fetchUser() {
      try {
        const token = localStorage.getItem('token');
        const { data } = await axios.get('http://localhost:8000/api/me', {
          headers: { Authorization: `Bearer ${this.token}` }
        })
        this.user = data
        this.permisos = data.permisos;
      } catch (error) {
        // Si el token es inválido o expiró, limpiamos todo
        this.token = null
        this.user = null
        localStorage.removeItem('token')
      }
    },
    
    async logout() {
      try {
        // Le avisamos a Laravel que destruya la llave en el servidor
        await axios.post('http://localhost:8000/api/logout', {}, {
          headers: { Authorization: `Bearer ${this.token}` }
        })
      } catch (error) {
        console.error("Error al cerrar sesión en el servidor")
      } finally {
        // Pase lo que pase, destruimos la llave en nuestro cliente
        this.token = null
        this.user = null
        localStorage.removeItem('token')
      }
    }
  }
})
