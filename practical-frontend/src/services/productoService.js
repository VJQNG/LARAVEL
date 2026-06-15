// Creamos la instancia base apuntando al puerto de Laravel
//const api = axios.create({
//    baseURL: 'http://localhost:8000/api'
//});

// 1. Importamos nuestra instancia interceptada (con el Token)
import api from '../plugins/axios'

// 2. Exportamos las funciones del CRUD
export const getProductos = (params) => api.get('/productos', { params });

export const createProducto = (data) => {
    return api.post('/productos', data);
};

export const updateProducto = (id, producto) => {
    if (producto instanceof FormData) {
        producto.append('_method', 'PUT'); // Truco para que Laravel lo trate como actualización
        return api.post(`/productos/${id}`, producto); // Sin headers manuales
    }

    // Si es una actualización normal de texto (desde la tabla de inventario)
    return api.put(`/productos/${id}`, producto);
}
export const deleteProducto = (id) => api.delete(`/productos/${id}`);
