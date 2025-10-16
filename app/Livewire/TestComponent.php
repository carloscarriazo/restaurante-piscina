<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\OrderService;
use App\Models\Order;
use App\Models\Table;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class TestComponent extends Component
{
    public $orders = [];
    public $message = '';
    public $testResult = '';

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        $orderService = app(OrderService::class);
        $this->orders = $orderService->getOrdersByWaiter();
    }

    public function createTestOrder()
    {
        try {
            // Verificar que estamos autenticados
            if (!Auth::check()) {
                $this->testResult = 'Error: Usuario no autenticado';
                return;
            }

            $orderService = app(OrderService::class);

            // Obtener una mesa disponible
            $table = Table::where('status', 'available')->first();
            if (!$table) {
                $this->testResult = 'Error: No hay mesas disponibles';
                return;
            }

            // Crear datos del pedido
            $orderData = [
                'table_id' => $table->id,
                'customer_name' => 'Cliente de Prueba',
                'notes' => 'Pedido de prueba desde TestComponent'
            ];

            // Crear el pedido
            $result = $orderService->create($orderData);

            if ($result['success']) {
                $order = $result['order'];

                // Agregar un producto al pedido
                $product = Product::where('available', true)->first();
                if ($product) {
                    $addResult = $orderService->addProduct(
                        $order->id,
                        $product->id,
                        2,
                        'Nota de prueba para el producto'
                    );

                    if ($addResult['success']) {
                        $this->testResult = "✅ Pedido creado exitosamente! ID: {$order->id}, Mesa: {$table->name}, Producto: {$product->name}";
                    } else {
                        $this->testResult = "⚠️ Pedido creado pero error al agregar producto: " . $addResult['message'];
                    }
                } else {
                    $this->testResult = "⚠️ Pedido creado pero no hay productos disponibles";
                }

                // Recargar órdenes
                $this->loadOrders();

            } else {
                $this->testResult = "❌ Error al crear pedido: " . $result['message'];
            }

        } catch (\Exception $e) {
            $this->testResult = "❌ Excepción: " . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.test-component');
    }
}
