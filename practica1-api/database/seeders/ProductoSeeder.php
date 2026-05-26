<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Producto::create([
            'nombre' => 'Laptop Lenovo Thinkpad',
            'descripcion' => 'Certificada para correr Arch Linux sin problemas.',
            'precio' => 15000.50,
            'stock' => 10
        ]);

        Producto::create([
            'nombre' => 'Teclado Mecánico',
            'descripcion' => 'Switches red, ideal para programar en vim.',
            'precio' => 1200.00,
            'stock' => 25
        ]);
    }
}
