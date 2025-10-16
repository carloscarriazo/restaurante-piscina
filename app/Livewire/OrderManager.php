<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Table;
use Illuminate\Support\Facades\Auth;

class OrderManager extends Component
{
    // Propiedades del componente
    public $orders = [];
    public $availableTables = [];
    public $products = [];
    public $categories = [];

    // Modal y formulario
    public $showModal = false;
    public $editingOrderId = null;

    // Formulario de pedido
    public $form = [
        'table_id' => '',
        'customer_name' => '',
        'notes' => ''
    ];

    // Carrito de productos
    public $cart = [];
    public $selectedCategoryId = '';
    public $searchProduct = '';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        try {
            $orderService = app(OrderService::class);
            $productService = app(ProductService::class);

            $this->orders = $orderService->getOrdersByWaiter();
            $this->availableTables = $orderService->getAvailableTables();
            $this->products = $productService->getAvailableProducts();
            $this->categories = $productService->getCategories();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar datos: ' . $e->getMessage());
            // Inicializar con arrays vacíos para evitar errores
            $this->orders = collect([]);
            $this->availableTables = collect([]);
            $this->products = collect([]);
            $this->categories = collect([]);
        }
    }

    /**
     * Abrir modal para nuevo pedido
     */
    public function createOrder()
    {
        $this->resetForm();
        $this->editingOrderId = null;
        $this->showModal = true;
        $this->loadData();
    }

    /**
     * Guardar pedido (crear o editar)
     */
    public function save()
    {
        $this->validate([
            'form.table_id' => 'required|exists:tables,id',
            'form.customer_name' => 'nullable|string|max:255',
            'form.notes' => 'nullable|string|max:500'
        ], [
            'form.table_id.required' => 'Debe seleccionar una mesa',
            'form.table_id.exists' => 'La mesa seleccionada no es válida'
        ]);

        if (empty($this->cart)) {
            session()->flash('error', 'Debe agregar al menos un producto al pedido');
            return;
        }

        try {
            if ($this->editingOrderId) {
                // Editar pedido existente
                $this->updateExistingOrder();
            } else {
                // Crear nuevo pedido
                $this->createNewOrder();
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Crear nuevo pedido
     */
    private function createNewOrder()
    {
        $orderService = app(OrderService::class);

        // Preparar datos del pedido incluyendo items
        $orderData = array_merge($this->form, [
            'items' => $this->cart
        ]);

        $result = $orderService->create($orderData);

        if ($result['success']) {

            $this->resetForm();
            $this->showModal = false;
            $this->loadData();
            $this->dispatch('order-created');
            session()->flash('message', 'Pedido creado exitosamente');
        } else {
            session()->flash('error', $result['message']);
        }
    }

    /**
     * Actualizar pedido existente
     */
    private function updateExistingOrder()
    {
        $orderService = app(OrderService::class);
        $order = $orderService->find($this->editingOrderId);

        if (!$order) {
            session()->flash('error', \App\Constants\MessageConstants::PRODUCT_NOT_FOUND);
            return;
        }

        // Actualizar información básica del pedido
        $order->update([
            'table_id' => $this->form['table_id'],
            'customer_name' => $this->form['customer_name'],
            'notes' => $this->form['notes'],
            'last_edited_at' => now(),
            'last_edited_by' => Auth::id()
        ]);

        // Eliminar items existentes
        $order->items()->delete();

        // Agregar nuevos items del carrito
        foreach ($this->cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'cantidad' => $item['quantity'],
                'precio' => $item['price'],
                'notas' => $item['notes'] ?? null
            ]);
        }

        // Recalcular total
        $total = $order->items->sum(function ($item) {
            return $item->cantidad * $item->precio;
        });
        $order->update(['total' => $total]);

        $this->resetForm();
        $this->showModal = false;
        $this->loadData();

        session()->flash('message', 'Pedido actualizado exitosamente');
    }

    /**
     * Editar pedido existente
     */
    public function editOrder($orderId)
    {
        try {
            $order = Order::with(['items.product', 'table'])->find($orderId);

            if (!$order) {
                session()->flash('error', 'Pedido no encontrado');
                return;
            }

            // Verificar si el pedido se puede editar (solo pedidos pendientes o en proceso)
            if (!in_array($order->status, ['pending', 'in_process'])) {
                session()->flash('error', 'Este pedido ya no se puede modificar (Estado: ' . $order->status . ')');
                return;
            }

            // Cargar datos del pedido en el formulario
            $this->editingOrderId = $order->id;
            $this->form = [
                'table_id' => $order->table_id,
                'customer_name' => $order->customer_name,
                'notes' => $order->notes
            ];

            // Cargar items en el carrito
            $this->cart = [];
            foreach ($order->items as $item) {
                $this->cart[] = [
                    'product_id' => $item->product_id,
                    'name' => $item->product->name ?? 'Producto eliminado',
                    'price' => $item->precio,
                    'quantity' => $item->cantidad,
                    'notes' => $item->notas ?? ''
                ];
            }

            $this->showModal = true;
            $this->loadData();

        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Agregar producto al carrito
     */
    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if (!$product || !$product->available) {
            session()->flash('error', 'El producto no está disponible');
            return;
        }

        $existingIndex = null;
        foreach ($this->cart as $index => $item) {
            if ($item['product_id'] == $productId) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            $this->cart[$existingIndex]['quantity']++;
        } else {
            $this->cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'notes' => ''
            ];
        }

        $this->dispatch('productAdded', $product->name);
    }

    /**
     * Actualizar cantidad en el carrito
     */
    public function updateCartQuantity($index, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($index);
        } else {
            $this->cart[$index]['quantity'] = (int) $quantity;
        }
    }

    /**
     * Remover producto del carrito
     */
    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart); // Reindexar array
    }

    /**
     * Limpiar carrito
     */
    public function clearCart()
    {
        $this->cart = [];
    }

    /**
     * Calcular total del carrito
     */
    public function getCartTotal()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    /**
     * Filtrar productos por categoría
     */
    public function filterByCategory($categoryId = null)
    {
        $this->selectedCategoryId = $categoryId;

        if ($categoryId) {
            $this->products = Product::where('category_id', $categoryId)
                                   ->where('available', true)
                                   ->get();
        } else {
            $this->products = $this->productService->getAvailableProducts();
        }
    }

    /**
     * Buscar productos
     */
    public function searchProducts()
    {
        if (empty($this->searchProduct)) {
            $this->products = $this->productService->getAvailableProducts();
        } else {
            $this->products = Product::where('name', 'like', '%' . $this->searchProduct . '%')
                                   ->where('available', true)
                                   ->get();
        }
    }

    /**
     * Cambiar estado del pedido
     */
    public function changeOrderStatus($orderId, $newStatus)
    {
        try {
            $orderService = app(OrderService::class);
            $result = $orderService->changeStatus($orderId, $newStatus);

            if ($result['success']) {
                $this->loadData();
                session()->flash('message', $result['message']);
            } else {
                session()->flash('error', $result['message']);
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }

    /**
     * Resetear formulario
     */
    private function resetForm()
    {
        $this->form = [
            'table_id' => '',
            'customer_name' => '',
            'notes' => ''
        ];
        $this->cart = [];
        $this->selectedCategoryId = '';
        $this->searchProduct = '';
    }

    /**
     * Refrescar datos manualmente
     */
    public function refreshOrders()
    {
        $this->loadData();
        session()->flash('message', 'Datos actualizados');
    }

    public function render()
    {
        return view('livewire.order-manager', [
            'cartTotal' => $this->getCartTotal()
        ]);
    }
}
