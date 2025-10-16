<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Login exitoso con credenciales válidas
     */
    public function test_login_with_valid_credentials(): void
    {
        // Crear rol de waiter
        $waiterRole = Role::create([
            'name' => 'waiter',
            'description' => 'Mesero del restaurante'
        ]);

        // Crear usuario de prueba
        $user = User::factory()->create([
            'email' => 'test@restaurant.com',
            'password' => bcrypt('password123')
        ]);
        $user->roles()->attach($waiterRole);

        // Intentar login
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@restaurant.com',
            'password' => 'password123'
        ]);

        // Verificar respuesta
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'roles'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true
            ]);

        // Verificar que el token fue creado
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class
        ]);
    }

    /**
     * Test: Login falla con credenciales inválidas
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@restaurant.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@restaurant.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false
            ]);
    }

    /**
     * Test: Login requiere validación de campos
     */
    public function test_login_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Test: Rate limiting en login (máximo 5 intentos por minuto)
     */
    public function test_login_rate_limiting(): void
    {
        $user = User::factory()->create([
            'email' => 'test@restaurant.com',
            'password' => bcrypt('password123')
        ]);

        // Hacer 6 intentos de login
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'email' => 'test@restaurant.com',
                'password' => 'wrongpassword'
            ]);

            if ($i < 5) {
                $response->assertStatus(422);
            } else {
                // El sexto intento debe ser bloqueado por rate limiting
                $response->assertStatus(429); // Too Many Requests
            }
        }
    }

    /**
     * Test: Obtener información del usuario autenticado
     */
    public function test_get_authenticated_user_info(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'roles'
                ]
            ]);
    }

    /**
     * Test: Logout elimina el token del usuario
     */
    public function test_logout_deletes_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Hacer logout
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        // Verificar que el token fue eliminado
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class
        ]);
    }

    /**
     * Test: Acceso no autorizado sin token
     */
    public function test_unauthorized_access_without_token(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    /**
     * Test: Registro de nuevo usuario
     */
    public function test_register_creates_new_user(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'newuser@restaurant.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ]
                ]
            ]);

        // Verificar que el usuario fue creado
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@restaurant.com'
        ]);
    }

    /**
     * Test: Registro requiere confirmación de contraseña
     */
    public function test_register_requires_password_confirmation(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'newuser@restaurant.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
