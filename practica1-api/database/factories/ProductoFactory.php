<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'nombre'       => $this->faker->words(3, true), // 3 palabras aleatorias
            'descripcion'  => $this->faker->paragraph(), // Un párrafo de texto falso
            'precio'       => $this->faker->randomFloat(2, 10, 500), // Precio entre $10 y $500
            'stock'        => $this->faker->numberBetween(0, 100), // Stock entre 0 y 100
            'categoria_id' => Categoria::factory(), // ¡Crea una categoría falsa al vuelo!
        ];
    }
}
