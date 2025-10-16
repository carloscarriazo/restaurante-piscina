<div class="space-y-6">
    <!-- Botones de Acción -->
    <div class="flex flex-wrap gap-3 justify-end">
        <button wire:click="createOrder" class="ocean-btn">
            <i class="fas fa-plus mr-2"></i>
            Nuevo Pedido
        </button>
        <button wire:click="loadData" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all">
            <i class="fas fa-sync-alt mr-2"></i>
            Actualizar
        </button>
    </div>

    <!-- Mensajes de alerta con estilo Ocean -->
    @if (session()->has('message'))
        <div class="bg-gradient-to-r from-green-400/20 to-emerald-500/20 border border-green-400/50 text-green-100 px-6 py-4 rounded-xl backdrop-blur-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-green-400"></i>
                {{ session('message') }}
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-gradient-to-r from-red-400/20 to-red-600/20 border border-red-400/50 text-red-100 px-6 py-4 rounded-xl backdrop-blur-sm">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3 text-red-400"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Estadísticas Rápidas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="ocean-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Pedidos Hoy</p>
                    <p class="text-3xl font-bold text-white">{{ count($orders) }}</p>
                </div>
                <i class="fas fa-shopping-cart text-3xl text-cyan-400"></i>
            </div>
        </div>
        <div class="ocean-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Pendientes</p>
                    <p class="text-3xl font-bold text-yellow-400">{{ $orders->where('status', 'pending')->count() }}</p>
                </div>
                <i class="fas fa-clock text-3xl text-yellow-400"></i>
            </div>
        </div>
        <div class="ocean-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">En Proceso</p>
                    <p class="text-3xl font-bold text-blue-400">{{ $orders->where('status', 'in_process')->count() }}</p>
                </div>
                <i class="fas fa-fire text-3xl text-blue-400"></i>
            </div>
        </div>
        <div class="ocean-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Listos</p>
                    <p class="text-3xl font-bold text-green-400">{{ $orders->where('status', 'ready')->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-3xl text-green-400"></i>
            </div>
        </div>
    </div>

    <!-- Pedidos activos -->
    <div class="ocean-card">
        <div class="px-6 py-5">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-white">
                    <i class="fas fa-list mr-2 text-cyan-400"></i>
                    Pedidos Activos
                </h3>
                <button wire:click="refreshOrders"
                        class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                    <i class="fas fa-sync-alt"></i>
                    Refrescar
                </button>
            </div>

            @if(count($orders) > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    @foreach($orders as $order)
                        @php
                            $statusConfig = [
                                'pending' => [
                                    'class' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/50',
                                    'label' => 'Pendiente',
                                    'icon' => 'fa-clock'
                                ],
                                'in_process' => [
                                    'class' => 'bg-blue-500/20 text-blue-300 border-blue-500/50',
                                    'label' => 'En Proceso',
                                    'icon' => 'fa-fire'
                                ],
                                'ready' => [
                                    'class' => 'bg-green-500/20 text-green-300 border-green-500/50',
                                    'label' => 'Listo',
                                    'icon' => 'fa-check-circle'
                                ],
                                'served' => [
                                    'class' => 'bg-gray-500/20 text-gray-300 border-gray-500/50',
                                    'label' => 'Servido',
                                    'icon' => 'fa-utensils'
                                ],
                            ];
                            $status = $statusConfig[$order->status] ?? $statusConfig['pending'];
                        @endphp
                        <div class="bg-white/5 border border-white/10 rounded-xl p-5 hover:bg-white/10 transition-all">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center">
                                        <i class="fas fa-table text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-white">
                                            {{ $order->table->name ?? 'N/A' }}
                                        </h4>
                                        <p class="text-sm text-gray-400">
                                            <i class="fas fa-user mr-1"></i>
                                            {{ $order->customer_name ?? 'Sin nombre' }}
                                        </p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $status['class'] }}">
                                    <i class="fas {{ $status['icon'] }} mr-1"></i>
                                    {{ $status['label'] }}
                                </span>
                            </div>

                            <!-- Items del pedido -->
                            @if(count($order->items) > 0)
                                <div class="bg-white/5 rounded-lg p-3 mb-4">
                                    <div class="space-y-2 max-h-32 overflow-y-auto">
                                        @foreach($order->items as $item)
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-300">
                                                    <span class="font-semibold text-cyan-400">{{ $item->cantidad }}x</span>
                                                    {{ $item->product->name ?? 'Producto eliminado' }}
                                                </span>
                                                <span class="text-white font-medium">${{ number_format($item->precio * $item->cantidad, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($order->notes)
                                <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-3 mb-4">
                                    <p class="text-sm text-yellow-200">
                                        <i class="fas fa-sticky-note mr-2"></i>
                                        <strong>Notas:</strong> {{ $order->notes }}
                                    </p>
                                </div>
                            @endif

                            <div class="flex items-center justify-between pt-3 border-t border-white/10">
                                <div class="text-sm text-gray-400">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $order->created_at->format('H:i') }}
                                    <span class="ml-2 text-xs">(Estado: {{ $order->status }})</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xl font-bold text-cyan-400">
                                        ${{ number_format($order->total, 0, ',', '.') }}
                                    </span>
                                    @if(in_array($order->status, ['pending', 'in_process']))
                                        <button wire:click="editOrder({{ $order->id }})"
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-all">
                                            <i class="fas fa-edit mr-1"></i>
                                            Editar
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 italic">
                                            No editable
                                        </span>
                                    @endif
                                    @if($order->status === 'ready')
                                        <button wire:click="changeOrderStatus({{ $order->id }}, 'delivered')"
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-all">
                                            <i class="fas fa-check mr-1"></i>
                                            Entregar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-cyan-400/20 to-blue-500/20 mb-4">
                        <i class="fas fa-clipboard-list text-4xl text-cyan-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">No hay pedidos activos</h3>
                    <p class="text-gray-400 mb-6">Comienza creando un nuevo pedido para tus clientes.</p>
                    <button wire:click="createOrder" class="ocean-btn">
                        <i class="fas fa-plus mr-2"></i>
                        Crear Primer Pedido
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para crear/editar pedido -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50">
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-6xl">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ $editingOrderId ? 'Editar Pedido' : 'Nuevo Pedido' }}
                                </h3>
                                <button wire:click="$set('showModal', false)"
                                        class="text-gray-400 hover:text-gray-600">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Panel izquierdo: Formulario -->
                                <div class="space-y-4">
                                    <div>
                                        <label for="order-table" class="block text-sm font-medium text-gray-700">Mesa</label>
                                        <select id="order-table"
                                                wire:model="form.table_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Seleccionar mesa</option>
                                            @foreach($availableTables as $table)
                                                <option value="{{ $table->id }}">{{ $table->name }} ({{ $table->capacity }} personas)</option>
                                            @endforeach
                                        </select>
                                        @error('form.table_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="order-customer-name" class="block text-sm font-medium text-gray-700">Nombre del Cliente (Opcional)</label>
                                        <input id="order-customer-name"
                                               type="text" wire:model="form.customer_name"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div>
                                        <label for="order-notes" class="block text-sm font-medium text-gray-700">Notas</label>
                                        <textarea id="order-notes"
                                                  wire:model="form.notes" rows="3"
                                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    </div>

                                    <!-- Carrito -->
                                    <div class="border-t pt-4">
                                        <h4 class="text-md font-medium text-gray-900 mb-3">Productos Seleccionados</h4>
                                        @if(count($cart) > 0)
                                            <div class="space-y-2 max-h-60 overflow-y-auto">
                                                @foreach($cart as $index => $item)
                                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                                        <div class="flex-1">
                                                            <span class="text-sm font-medium">{{ $item['name'] }}</span>
                                                            <div class="text-xs text-gray-500">${{ number_format($item['price'], 0, ',', '.') }} c/u</div>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <input type="number" min="1"
                                                                   wire:change="updateCartQuantity({{ $index }}, $event.target.value)"
                                                                   value="{{ $item['quantity'] }}"
                                                                   class="w-16 rounded border-gray-300 text-center text-sm">
                                                            <button wire:click="removeFromCart({{ $index }})"
                                                                    class="text-red-500 hover:text-red-700">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="border-t mt-3 pt-3">
                                                <div class="flex justify-between items-center">
                                                    <span class="text-lg font-semibold">Total:</span>
                                                    <span class="text-lg font-bold text-indigo-600">${{ number_format($cartTotal, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-gray-500 text-sm">No hay productos seleccionados</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Panel derecho: Selección de productos -->
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="text-md font-medium text-gray-900 mb-3">Agregar Productos</h4>

                                        <!-- Filtros -->
                                        <div class="space-y-3 mb-4">
                                            <div>
                                                <input type="text" wire:model.live="searchProduct"
                                                       wire:keyup="searchProducts"
                                                       placeholder="Buscar productos..."
                                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <button wire:click="filterByCategory()"
                                                        class="px-3 py-1 text-xs rounded-full border {{ !$selectedCategoryId ? 'bg-indigo-100 text-indigo-800 border-indigo-200' : 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                                    Todas
                                                </button>
                                                @foreach($categories as $category)
                                                    <button wire:click="filterByCategory({{ $category->id }})"
                                                            class="px-3 py-1 text-xs rounded-full border {{ $selectedCategoryId == $category->id ? 'bg-indigo-100 text-indigo-800 border-indigo-200' : 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                                        {{ $category->nombre }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Lista de productos -->
                                        <div class="max-h-96 overflow-y-auto space-y-2">
                                            @foreach($products as $product)
                                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded hover:bg-gray-50">
                                                    <div class="flex-1">
                                                        <div class="font-medium text-sm">{{ $product->name }}</div>
                                                        <div class="text-xs text-gray-500">{{ $product->description }}</div>
                                                        <div class="text-sm font-semibold text-indigo-600">${{ number_format($product->price, 0, ',', '.') }}</div>
                                                    </div>
                                                    <button wire:click="addToCart({{ $product->id }})"
                                                            class="ml-3 inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 transition ease-in-out duration-150">
                                                        Agregar
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones del modal -->
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button wire:click="save"
                                    class="inline-flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $editingOrderId ? 'Actualizar' : 'Crear Pedido' }}
                            </button>
                            <button wire:click="$set('showModal', false)"
                                    class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('productAdded', (productName) => {
            // Aquí puedes agregar notificaciones toast si las tienes configuradas
            console.log('Producto agregado:', productName);
        });
    });
</script>
