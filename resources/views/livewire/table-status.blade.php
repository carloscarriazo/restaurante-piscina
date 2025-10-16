<div class="space-y-3">
    @forelse($tables as $table)
        <div class="border rounded-lg p-3 transition-all hover:shadow-md
                    @if($table['status'] === 'available') bg-green-50 border-green-200
                    @elseif($table['status'] === 'occupied') bg-red-50 border-red-200
                    @elseif($table['status'] === 'cleaning') bg-yellow-50 border-yellow-200
                    @else bg-gray-50 border-gray-200 @endif">

            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2">
                    <span class="font-semibold text-gray-900">Mesa {{ $table['number'] }}</span>

                    <!-- Indicador de estado -->
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                                @if($table['status'] === 'available') bg-green-100 text-green-800
                                @elseif($table['status'] === 'occupied') bg-red-100 text-red-800
                                @elseif($table['status'] === 'cleaning') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                        {{ $table['status_label'] }}
                    </span>
                </div>

                <!-- Botones de acción -->
                <div class="flex items-center space-x-1">
                    @if($table['can_occupy'])
                        <button wire:click="toggleTableStatus({{ $table['id'] }})"
                                class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">
                            Ocupar
                        </button>
                    @elseif($table['can_free'])
                        <button wire:click="toggleTableStatus({{ $table['id'] }})"
                                class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                            Liberar
                        </button>
                    @endif
                </div>
            </div>

            <!-- Información adicional -->
            <div class="text-sm text-gray-600 space-y-1">
                <div class="flex items-center justify-between">
                    <span>Capacidad: {{ $table['capacity'] }} personas</span>
                    @if($table['location'])
                        <span class="text-xs">{{ $table['location'] }}</span>
                    @endif
                </div>

                @if($table['is_occupied'] && $table['occupied_since'])
                    <div class="text-xs text-gray-500">
                        Ocupada desde: {{ $table['occupied_since'] }}
                    </div>
                @endif

                <!-- Información del pedido actual -->
                @if($table['current_order'])
                    <div class="mt-2 p-2 bg-white rounded border border-gray-200">
                        <div class="text-xs">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-medium">{{ $table['current_order']['code'] }}</span>
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full
                                            @if($table['current_order']['status'] === 'pending') bg-blue-100 text-blue-800
                                            @elseif($table['current_order']['status'] === 'preparing') bg-orange-100 text-orange-800
                                            @elseif($table['current_order']['status'] === 'ready') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($table['current_order']['status']) }}
                                </span>
                            </div>
                            <div class="text-gray-600">
                                <p>Mesero: {{ $table['current_order']['waiter'] }}</p>
                                <p>{{ $table['current_order']['items_count'] }} productos • ${{ number_format($table['current_order']['total'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-4 text-gray-500">
            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <p class="text-sm">No hay mesas registradas</p>
        </div>
    @endforelse

    <!-- Resumen rápido -->
    @if(count($tables) > 0)
        <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
            <h4 class="text-sm font-medium text-gray-900 mb-2">Resumen de Mesas</h4>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                    <span>Disponibles: {{ collect($tables)->where('status', 'available')->count() }}</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                    <span>Ocupadas: {{ collect($tables)->where('status', 'occupied')->count() }}</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                    <span>Limpieza: {{ collect($tables)->where('status', 'cleaning')->count() }}</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-gray-500 rounded-full mr-2"></div>
                    <span>Total: {{ count($tables) }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Botón de actualización -->
    <div class="mt-4 text-center">
        <button wire:click="loadTables"
                class="text-sm text-blue-600 hover:text-blue-800 focus:outline-none">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Actualizar Estado
        </button>
    </div>
</div>