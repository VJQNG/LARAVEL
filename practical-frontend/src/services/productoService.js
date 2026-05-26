import axios from 'axios';

// Creamos la instancia base apuntando al puerto de Laravel
const api = axios.create({
    baseURL: 'http://localhost:8000/api'
});

// Exportamos las 4 funciones del CRUD
export const getProductos = () => api.get('/productos');
export const createProducto = (data) => api.post('/productos', data);
export const updateProducto = (id, data) => api.put(`/productos/${id}`, data);
export const deleteProducto = (id) => api.delete(`/productos/${id}`);
