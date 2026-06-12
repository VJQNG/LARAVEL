<?php

namespace Tests\Feature;

use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriaTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_listar_categorias(): void
    {
        // 1. Fabricamos 3 categorías
        Categoria::factory(3)->create();

        // 2. Simulamos la petición al CategoriaController
        $response = $this->getJson('/api/categorias');

        // 3. Verificamos que el controlador responda OK y devuelva las 3
        $response->assertOk()
                 ->assertJsonCount(3, 'data');
    }

    public function test_admin_puede_crear_categoria(): void
    {
        // 1. Creamos el administrador
        $admin = \App\Models\User::factory()->create(['rol' => 'admin']);

        // 2. Simulamos la petición POST
        $this->actingAs($admin, 'sanctum')
             ->postJson('/api/categorias', [
                 'nombre' => 'Hardware Táctico'
             ])
             ->assertCreated(); // Esperamos un 201 Created

        // 3. Verificamos en la RAM
        $this->assertDatabaseHas('categorias', [
            'nombre' => 'Hardware Táctico'
        ]);
    }
}
