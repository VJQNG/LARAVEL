import * as yup from 'yup'

export const productoSchema = yup.object({
  nombre: yup.string()
    .required('El nombre es obligatorio')
    .min(3, 'Mínimo 3 caracteres')
    .max(100, 'Máximo 100 caracteres'),
    
  precio: yup.number()
    // Si el input está vacío, lo convertimos a null para que no marque error de tipo
    .transform((value, originalValue) => String(originalValue).trim() === '' ? null : value)
    .nullable()
    .optional()
    .min(0, 'El precio no puede ser negativo')
    .typeError('Debe ser un número'),
    
  stock: yup.number()
    .transform((value, originalValue) => String(originalValue).trim() === '' ? null : value)
    .nullable()
    .optional()
    .integer('Debe ser un número entero')
    .min(0, 'El stock no puede ser negativo')
    .typeError('Debe ser un número entero'),
    
  descripcion: yup.string()
    .max(500, 'Máximo 500 caracteres')
    .optional(),

  categoria_id: yup.number()
    .typeError('Debes seleccionar una categoría')
    .required('La categoría es obligatoria')
})
