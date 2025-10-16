<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">Panel de Cocina</h1>
            <div class="flex items-center space-x-4">
                <button wire:click="refreshOrders"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Actualizar
                </button>
                <span class="text-sm text-gray-500">
                    Última actualización: {{ now()->format('H:i:s') }}
                </span>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-semibold text-gray-900">{{ count($preparingOrders) }}</p>
                        <p class="text-gray-600">En Preparación</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-semibold text-gray-900">{{ count($readyOrders) }}</p>
                        <p class="text-gray-600">Listos para Servir</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-semibold text-gray-900">{{ count($orders) }}</p>
                        <p class="text-gray-600">Total Activos</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pedidos en dos columnas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pedidos en Preparación -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 bg-orange-500 rounded-full mr-2"></div>
                        En Preparación ({{ count($preparingOrders) }})
                    </h2>
                </div>

                <div class="p-6 max-h-96 overflow-y-auto space-y-4">
                    @forelse($preparingOrders as $order)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow
                                    @if($order['priority'] === 'high') border-l-4 border-l-red-500
                                    @elseif($order['priority'] === 'medium') border-l-4 border-l-yellow-500
                                    @else border-l-4 border-l-green-500 @endif">

                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold text-gray-900">{{ $order['code'] }}</span>
                                    <span class="text-sm text-gray-500">{{ $order['table_name'] }}</span>

                                    @if($order['priority'] === 'high')
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                            Urgente
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center space-x-2">
                                    <button wire:click="viewOrderDetails({{ $order['id'] }})"
                                            class="text-blue-600 hover:text-blue-800 text-sm">
                                        Ver detalles
                                    </button>
                                    <button wire:click="markAsReady({{ $order['id'] }})"
                                            class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Listo
                                    </button>
                                </div>
                            </div>

                            <div class="text-sm text-gray-600 mb-2">
                                <p>Mesero: {{ $order['waiter_name'] }}</p>
                                <p>{{ $order['items_count'] }} productos • Total: ${{ number_format($order['total'], 2) }}</p>
                            </div>

                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>Recibido: {{ $order['created_at'] }}</span>
                                <span class="@if($order['priority'] === 'high') text-red-600 font-medium @endif">
                                    {{ $order['time_elapsed'] }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                            </svg>
                            <p>No hay pedidos en preparación</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pedidos Listos -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        Listos para Servir ({{ count($readyOrders) }})
                    </h2>
                </div>

                <div class="p-6 max-h-96 overflow-y-auto space-y-4">
                    @forelse($readyOrders as $order)
                        <div class="border border-green-200 rounded-lg p-4 bg-green-50 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold text-gray-900">{{ $order['code'] }}</span>
                                    <span class="text-sm text-gray-500">{{ $order['table_name'] }}</span>
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        ¡Listo!
                                    </span>
                                </div>

                                <button wire:click="viewOrderDetails({{ $order['id'] }})"
                                        class="text-blue-600 hover:text-blue-800 text-sm">
                                    Ver detalles
                                </button>
                            </div>

                            <div class="text-sm text-gray-600 mb-2">
                                <p>Mesero: {{ $order['waiter_name'] }}</p>
                                <p>{{ $order['items_count'] }} productos • Total: ${{ number_format($order['total'], 2) }}</p>
                            </div>

                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>Recibido: {{ $order['created_at'] }}</span>
                                <span class="text-green-600 font-medium">{{ $order['time_elapsed'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>No hay pedidos listos</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de detalles del pedido -->
    @if($showOrderDetails && $selectedOrder)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeOrderDetails"></div>

                <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Detalles del Pedido {{ $selectedOrder['code'] }}
                        </h3>
                        <button wire:click="closeOrderDetails" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Mesa</p>
                                <p class="text-base text-gray-900">{{ $selectedOrder['table_name'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Mesero</p>
                                <p class="text-base text-gray-900">{{ $selectedOrder['waiter_name'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Estado</p>
                                <span class="inline-flex items-center px-2 py-1 text-sm font-medium rounded-full
                                    @if($selectedOrder['status'] === 'preparing') bg-orange-100 text-orange-800
                                    @elseif($selectedOrder['status'] === 'ready') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($selectedOrder['status']) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Recibido</p>
                                <p class="text-base text-gray-900">{{ $selectedOrder['created_at'] }}</p>
                            </div>
                        </div>

                        @if($selectedOrder['notes'])
                            <div class="p-4 bg-yellow-50 rounded-lg">
                                <p class="text-sm font-medium text-yellow-800 mb-2">Notas especiales:</p>
                                <p class="text-sm text-yellow-700">{{ $selectedOrder['notes'] }}</p>
                            </div>
                        @endif

                        <div>
                            <h4 class="text-base font-medium text-gray-900 mb-3">Productos del pedido</h4>
                            <div class="space-y-2">
                                @foreach($selectedOrder['items'] as $item)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $item['product_name'] }}</p>
                                            @if($item['notes'])
                                                <p class="text-sm text-gray-600">Nota: {{ $item['notes'] }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-gray-900">x{{ $item['quantity'] }}</p>
                                            <p class="text-sm text-gray-600">${{ number_format($item['total'], 2) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-100 rounded-lg">
                            <span class="text-lg font-semibold text-gray-900">Total del pedido:</span>
                            <span class="text-xl font-bold text-gray-900">${{ number_format($selectedOrder['total'], 2) }}</span>
                        </div>

                        @if($selectedOrder['status'] === 'preparing')
                            <div class="flex justify-end space-x-3 pt-4">
                                <button wire:click="closeOrderDetails"
                                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                                    Cerrar
                                </button>
                                <button wire:click="markAsReady({{ $selectedOrder['id'] }})"
                                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    Marcar como Listo
                                </button>
                            </div>
                        @else
                            <div class="flex justify-end pt-4">
                                <button wire:click="closeOrderDetails"
                                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                                    Cerrar
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>