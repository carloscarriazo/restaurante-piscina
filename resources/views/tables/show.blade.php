<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center bg-gradient-to-r from-blue-600 via-cyan-600 to-teal-600 p-6 rounded-lg shadow-lg">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight">
                    🪑 {{ __('Detalles de la Mesa') }}
                </h2>
                <p class="text-blue-100 mt-1">Información completa de la mesa {{ $table->number }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('tables.edit', $table) }}"
                   class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold py-3 px-6 rounded-full shadow-lg transform transition hover:scale-105 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('tables.index') }}"
                   class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-3 px-6 rounded-full shadow-lg transform transition hover:scale-105 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Información Principal de la Mesa -->
            <div class="card-ocean">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Número -->
                    <div class="text-center p-6 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-lg border border-cyan-400/30">
                        <div class="text-cyan-400 text-4xl mb-2">🪑</div>
                        <h3 class="text-gray-400 text-sm font-medium mb-1">Número</h3>
                        <p class="text-white text-2xl font-bold">{{ $table->number }}</p>
                    </div>

                    <!-- Nombre -->
                    <div class="text-center p-6 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-lg border border-blue-400/30">
                        <div class="text-blue-400 text-4xl mb-2">📝</div>
                        <h3 class="text-gray-400 text-sm font-medium mb-1">Nombre</h3>
                        <p class="text-white text-xl font-bold">{{ $table->name }}</p>
                    </div>

                    <!-- Capacidad -->
                    <div class="text-center p-6 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-lg border border-purple-400/30">
                        <div class="text-purple-400 text-4xl mb-2">👥</div>
                        <h3 class="text-gray-400 text-sm font-medium mb-1">Capacidad</h3>
                        <p class="text-white text-2xl font-bold">{{ $table->capacity }} personas</p>
                    </div>

                    <!-- Estado -->
                    <div class="text-center p-6 bg-gradient-to-br from-pink-500/20 to-red-500/20 rounded-lg border border-pink-400/30">
                        <div class="text-pink-400 text-4xl mb-2">
                            @if($table->is_available)
                                ✅
                            @else
                                ⛔
                            @endif
                        </div>
                        <h3 class="text-gray-400 text-sm font-medium mb-1">Estado</h3>
                        <p class="text-white text-xl font-bold">
                            @if($table->is_available)
                                <span class="text-green-400">Disponible</span>
                            @else
                                <span class="text-red-400">Ocupada</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="mt-6 pt-6 border-t border-white/10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-gray-400 text-sm font-medium mb-2">📍 Ubicación</h4>
                            <p class="text-white text-lg">
                                @if($table->location)
                                    {{ $table->location }}
                                @else
                                    <span class="text-gray-500 italic">Sin ubicación especificada</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <h4 class="text-gray-400 text-sm font-medium mb-2">📊 Estado General</h4>
                            <p class="text-white text-lg">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold
                                    @if($table->status === 'available')
                                        bg-green-500/20 text-green-400 border border-green-400/30
                                    @elseif($table->status === 'occupied')
                                        bg-red-500/20 text-red-400 border border-red-400/30
                                    @elseif($table->status === 'reserved')
                                        bg-yellow-500/20 text-yellow-400 border border-yellow-400/30
                                    @else
                                        bg-gray-500/20 text-gray-400 border border-gray-400/30
                                    @endif
                                ">
                                    {{ ucfirst($table->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid de 2 Columnas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Órdenes Recientes -->
                <div class="card-ocean">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/20">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <span class="text-2xl">📋</span>
                            Órdenes Recientes
                        </h3>
                        <span class="px-3 py-1 bg-cyan-500/20 text-cyan-400 rounded-full text-sm font-semibold border border-cyan-400/30">
                            {{ $table->orders->count() }}
                        </span>
                    </div>

                    @if($table->orders->count() > 0)
                        <div class="space-y-4">
                            @foreach($table->orders as $order)
                                <div class="p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-all">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h4 class="text-white font-semibold">Orden #{{ $order->id }}</h4>
                                            <p class="text-gray-400 text-sm">
                                                {{ $order->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            @if($order->status === 'pending')
                                                bg-yellow-500/20 text-yellow-400
                                            @elseif($order->status === 'in_process')
                                                bg-blue-500/20 text-blue-400
                                            @elseif($order->status === 'ready')
                                                bg-green-500/20 text-green-400
                                            @elseif($order->status === 'served')
                                                bg-purple-500/20 text-purple-400
                                            @elseif($order->status === 'completed')
                                                bg-green-500/20 text-green-400
                                            @else
                                                bg-red-500/20 text-red-400
                                            @endif
                                        ">
                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-white/10">
                                        <span class="text-gray-400 text-sm">Total:</span>
                                        <span class="text-cyan-400 font-bold text-lg">${{ number_format($order->total, 0) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-6xl mb-4 opacity-50">📋</div>
                            <p class="text-gray-400">No hay órdenes recientes</p>
                        </div>
                    @endif
                </div>

                <!-- Reservaciones Recientes -->
                <div class="card-ocean">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/20">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <span class="text-2xl">📅</span>
                            Reservaciones Recientes
                        </h3>
                        <span class="px-3 py-1 bg-purple-500/20 text-purple-400 rounded-full text-sm font-semibold border border-purple-400/30">
                            {{ $table->reservations->count() }}
                        </span>
                    </div>

                    @if($table->reservations->count() > 0)
                        <div class="space-y-4">
                            @foreach($table->reservations as $reservation)
                                <div class="p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-all">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h4 class="text-white font-semibold">{{ $reservation->customer_name }}</h4>
                                            <p class="text-gray-400 text-sm">
                                                📞 {{ $reservation->customer_phone ?? 'Sin teléfono' }}
                                            </p>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            @if($reservation->status === 'confirmed')
                                                bg-green-500/20 text-green-400
                                            @elseif($reservation->status === 'pending')
                                                bg-yellow-500/20 text-yellow-400
                                            @elseif($reservation->status === 'cancelled')
                                                bg-red-500/20 text-red-400
                                            @else
                                                bg-gray-500/20 text-gray-400
                                            @endif
                                        ">
                                            {{ ucfirst($reservation->status) }}
                                        </span>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-white/10 grid grid-cols-2 gap-2 text-sm">
                                        <div>
                                            <span class="text-gray-400">Fecha:</span>
                                            <span class="text-white ml-1">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400">Personas:</span>
                                            <span class="text-white ml-1">{{ $reservation->party_size }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-6xl mb-4 opacity-50">📅</div>
                            <p class="text-gray-400">No hay reservaciones recientes</p>
                        </div>
                    @endif
                </div>

            </div>

            <!-- Acciones Rápidas -->
            <div class="card-ocean">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <span class="text-2xl">⚡</span>
                    Acciones Rápidas
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="{{ route('tables.edit', $table) }}"
                       class="btn-ocean p-6 text-center flex flex-col items-center gap-3 hover:transform hover:scale-105 transition-all">
                        <span class="text-3xl">✏️</span>
                        <span class="font-semibold">Editar Mesa</span>
                    </a>

                    <a href="{{ route('tables.index') }}"
                       class="btn-ocean-secondary p-6 text-center flex flex-col items-center gap-3 hover:transform hover:scale-105 transition-all">
                        <span class="text-3xl">�</span>
                        <span class="font-semibold">Ver Todas las Mesas</span>
                    </a>

                    <form action="{{ route('tables.destroy', $table) }}" method="POST"
                          onsubmit="return confirm('¿Estás seguro de eliminar esta mesa?')"
                          class="w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-red-500/20 to-red-600/20 hover:from-red-500/30 hover:to-red-600/30 text-red-400 border border-red-500/30 p-6 rounded-lg text-center flex flex-col items-center gap-3 hover:transform hover:scale-105 transition-all">
                            <span class="text-3xl">🗑️</span>
                            <span class="font-semibold">Eliminar Mesa</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
