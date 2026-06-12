<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoTest extends TestCase
{
    use RefreshDatabase;

    // Función auxiliar para "loguearnos" mágicamente como administrador en los tests
    private function actingAsAdmin()
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        return $this->actingAs($admin, 'sanctum');
    }

    public function test_puede_listar_productos(): void
    {
        // Fabricamos 5 productos falsos en la RAM
        Producto::factory(5)->create();

        // Simulamos la petición del catálogo
        $this->actingAsAdmin()
             ->getJson('/api/productos')
             ->assertOk()
             ->assertJsonCount(5, 'data'); // Verificamos que lleguen los 5
    }

    public function test_puede_crear_producto(): void
    {
        // Creamos una categoría falsa para cumplir con tu FormRequest
        $categoria = Categoria::factory()->create();

        $this->actingAsAdmin()
             ->postJson('/api/productos', [
                 'nombre'       => 'Laptop Dell',
                 'precio'       => 1299.99,
                 'stock'        => 10,
                 'categoria_id' => $categoria->id, // Dependencia satisfecha
             ])
             ->assertCreated() // Esperamos un 201 Created
             ->assertJsonFragment(['nombre' => 'Laptop Dell']);

        // Buscamos directamente en la base de datos RAM si existe
        $this->assertDatabaseHas('productos', [
            'nombre' => 'Laptop Dell'
        ]);
    }

    public function test_cliente_no_puede_eliminar(): void
    {
        $cliente = User::factory()->create(['rol' => 'cliente']);
        $producto = Producto::factory()->create();

        // Un cliente intenta hacer un DELETE
        $this->actingAs($cliente, 'sanctum')
             ->deleteJson("/api/productos/{$producto->id}")
             ->assertForbidden(); // Esperamos que tu Policy lo patee (403)
    }

    public function test_admin_puede_actualizar_producto(): void
    {
        // 1. Creamos un producto original y una categoría nueva
        $producto = Producto::factory()->create();
        $categoria = Categoria::factory()->create();

        // 2. Simulamos enviar un PUT para editarlo
        $this->actingAsAdmin()
             ->putJson("/api/productos/{$producto->id}", [
                 'nombre'       => 'Producto Actualizado',
                 'precio'       => 50.00,
                 'stock'        => 20,
                 'categoria_id' => $categoria->id,
             ])
             ->assertOk(); // Esperamos un 200 OK

        // 3. Verificamos que el cambio se guardó en la base de datos RAM
        $this->assertDatabaseHas('productos', [
            'nombre' => 'Producto Actualizado'
        ]);
    }

    public function test_admin_puede_eliminar_producto_exitosamente(): void
    {
        // 1. Creamos un producto
        $producto = Producto::factory()->create();

        // 2. Simulamos un DELETE
        $this->actingAsAdmin()
             ->deleteJson("/api/productos/{$producto->id}")
             ->assertNoContent();

        // 3. Verificamos que el producto YA NO EXISTA en la base de datos RAM
        $this->assertDatabaseMissing('productos', [
            'id' => $producto->id
        ]);
    }

    public function test_falla_validacion_al_crear_producto_con_datos_vacios(): void
    {
        // Simulamos enviar un POST sin datos (array vacío)
        $this->actingAsAdmin()
             ->postJson('/api/productos', [])
             ->assertStatus(422) // Esperamos el código 422 de "Unprocessable Entity"
             ->assertJsonValidationErrors(['nombre', 'categoria_id']); // Esperamos que se queje de estos campos obligatorios
    }
}
