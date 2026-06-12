<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    // ¡El comando mágico que formatea la base de datos RAM en cada prueba!
    use RefreshDatabase; 

    /**
     * Prueba 1: Un usuario nuevo debería poder registrarse correctamente.
     */
    public function test_usuario_puede_registrarse(): void
    {
        // Simulamos una petición POST de un cliente
        $response = $this->postJson('/api/register', [
            'name'                  => 'Juan López',
            'email'                 => 'juan@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Verificamos que el servidor responda "201 Created"
        $response->assertStatus(201)
                 ->assertJsonStructure(['token', 'user']);

        // Verificamos directamente en la base de datos si se guardó
        $this->assertDatabaseHas('users', [
            'email' => 'juan@test.com'
        ]);
    }

    /**
     * Prueba 2: El sistema debe rechazar credenciales falsas.
     */
    public function test_login_con_credenciales_incorrectas(): void
    {
        // Simulamos un intento de hackeo o error de tipeo
        $response = $this->postJson('/api/login', [
            'email'    => 'noexiste@test.com',
            'password' => 'wrongpass',
        ]);

        // Verificamos que el servidor responda "401 Unauthorized"
        $response->assertStatus(401);
    }

    public function test_login_con_credenciales_correctas(): void
    {
        // 1. Fabricamos un usuario con una contraseña conocida
        $user = \App\Models\User::factory()->create([
            'email' => 'marcos@archlinux.org',
            'password' => bcrypt('supersecreto123'),
        ]);

        // 2. Simulamos el inicio de sesión exitoso
        $response = $this->postJson('/api/login', [
            'email'    => 'marcos@archlinux.org',
            'password' => 'supersecreto123',
        ]);

        // 3. Verificamos que devuelva 200 OK y la estructura con el Token
        $response->assertOk()
                 ->assertJsonStructure(['token', 'user']);
    }
}
