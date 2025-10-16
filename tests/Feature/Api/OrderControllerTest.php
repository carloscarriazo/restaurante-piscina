<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Order;
use App\Models\Table;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear usuario autenticado para tests
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /**
     * Test: Listar órdenes con autenticación
     */
    public function test_list_orders_requires_authentication(): void
    {
        $response = $this->getJson('/api/orders');
        $response->assertStatus(401);
    }

    /**
     * Test: Listar órdenes autenticado
     */
    public function test_authenticated_user_can_list_orders(): void
    {
        // Crear mesa y órdenes de prueba
        $table = Table::factory()->create();
        Order::factory()->count(3)->create([
            'table_id' => $table->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'table_id',
                        'user_id',
                        'status',
                        'total'
                    ]
                ]
            ]);
    }

    /**
     * Test: Crear orden requiere mesa válida
     */
    public function test_create_order_requires_valid_table(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/orders', [
                'table_id' => 999, // Mesa inexistente
                'items' => []
            ]);

        $response->assertStatus(422);
    }

    /**
     * Test: Crear orden exitosamente
     */
    public function test_create_order_successfully(): void
    {
        $table = Table::factory()->create(['status' => 'available']);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 100.00,
            'is_available' => true
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/orders', [
                'table_id' => $table->id,
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 2,
                        'unit_price' => 100.00
                    ]
                ]
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'table_id',
                    'user_id',
                    'status',
                    'total',
                    'items'
                ]
            ]);

        // Verificar que la orden fue creada en DB
        $this->assertDatabaseHas('orders', [
            'table_id' => $table->id,
            'user_id' => $this->user->id
        ]);
    }

    /**
     * Test: Ver detalle de orden
     */
    public function test_show_order_details(): void
    {
        $table = Table::factory()->create();
        $order = Order::factory()->create([
            'table_id' => $table->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'table_id',
                    'user_id',
                    'status',
                    'total'
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $order->id
                ]
            ]);
    }

    /**
     * Test: Actualizar estado de orden
     */
    public function test_update_order_status(): void
    {
        $table = Table::factory()->create();
        $order = Order::factory()->create([
            'table_id' => $table->id,
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->patchJson("/api/orders/{$order->id}/status", [
                'status' => 'preparing'
            ]);

        $response->assertStatus(200);

        // Verificar que el estado se actualizó
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'preparing'
        ]);
    }

    /**
     * Test: Eliminar orden
     */
    public function test_delete_order(): void
    {
        $table = Table::factory()->create();
        $order = Order::factory()->create([
            'table_id' => $table->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(200);

        // Verificar que la orden fue eliminada
        $this->assertDatabaseMissing('orders', [
            'id' => $order->id
        ]);
    }

    /**
     * Test: Rate limiting en endpoints protegidos
     */
    public function test_api_rate_limiting(): void
    {
        // Hacer 61 requests (límite es 60/minuto)
        for ($i = 0; $i < 61; $i++) {
            $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                ->getJson('/api/orders');

            if ($i < 60) {
                $response->assertStatus(200);
            } else {
                // Request 61 debe ser bloqueado
                $response->assertStatus(429);
            }
        }
    }
}
