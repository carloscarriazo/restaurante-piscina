<x-app-layout>
    <x-slot name="title">Gestión de Mesas</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header con estilo Ocean -->
        <div class="ocean-card p-6 mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        <i class="fas fa-table mr-3 text-cyan-400"></i>
                        Gestión de Mesas
                    </h1>
                    <p class="text-gray-300">Administra las mesas del restaurante</p>
                </div>
                <a href="{{ route('tables.create') }}"
                   class="ocean-btn inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Nueva Mesa
                </a>
            </div>
        </div>

        <!-- Mensajes de alerta con estilo Ocean -->
        @if(session('success'))
            <div class="bg-gradient-to-r from-green-400/20 to-emerald-500/20 border border-green-400/50 text-green-100 px-6 py-4 rounded-xl backdrop-blur-sm mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-400 text-xl"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-gradient-to-r from-red-400/20 to-red-600/20 border border-red-400/50 text-red-100 px-6 py-4 rounded-xl backdrop-blur-sm mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-red-400 text-xl"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="ocean-card p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-300 text-sm">Total Mesas</p>
                        <p class="text-3xl font-bold text-white">{{ $tables->total() }}</p>
                    </div>
                    <i class="fas fa-table text-4xl text-cyan-400"></i>
                </div>
            </div>
            <div class="ocean-card p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-300 text-sm">Disponibles</p>
                        <p class="text-3xl font-bold text-green-400">{{ $tables->where('status', 'available')->count() }}</p>
                    </div>
                    <i class="fas fa-check-circle text-4xl text-green-400"></i>
                </div>
            </div>
            <div class="ocean-card p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-300 text-sm">Ocupadas</p>
                        <p class="text-3xl font-bold text-red-400">{{ $tables->where('status', 'occupied')->count() }}</p>
                    </div>
                    <i class="fas fa-users text-4xl text-red-400"></i>
                </div>
            </div>
            <div class="ocean-card p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-300 text-sm">Reservadas</p>
                        <p class="text-3xl font-bold text-yellow-400">{{ $tables->where('status', 'reserved')->count() }}</p>
                    </div>
                    <i class="fas fa-calendar-check text-4xl text-yellow-400"></i>
                </div>
            </div>
        </div>

        <!-- Filtros con diseño Ocean -->
        <div class="ocean-card p-6 mb-8">
            <form method="GET" action="{{ route('tables.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-search mr-1"></i>Buscar
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:border-cyan-400 focus:ring focus:ring-cyan-400/50"
                               placeholder="Número o nombre">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-info-circle mr-1"></i>Estado
                        </label>
                        <select name="status" id="status"
                                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:border-cyan-400 focus:ring focus:ring-cyan-400/50">
                            <option value="">Todos</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Disponible</option>
                            <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Ocupada</option>
                            <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reservada</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Mantenimiento</option>
                        </select>
                    </div>
                    <div>
                        <label for="is_available" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-toggle-on mr-1"></i>Disponibilidad
                        </label>
                        <select name="is_available" id="is_available"
                                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:border-cyan-400 focus:ring focus:ring-cyan-400/50">
                            <option value="">Todas</option>
                            <option value="1" {{ request('is_available') === '1' ? 'selected' : '' }}>Habilitadas</option>
                            <option value="0" {{ request('is_available') === '0' ? 'selected' : '' }}>Deshabilitadas</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full ocean-btn">
                            <i class="fas fa-search mr-2"></i>
                            Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Grid de Mesas con diseño Ocean -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($tables as $table)
                <div class="ocean-card p-6 hover:scale-105 transition-transform">
                    <!-- Header de la mesa -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center">
                                <i class="fas fa-table text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Mesa {{ $table->number }}</h3>
                                <p class="text-gray-400 text-sm">{{ $table->name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Estado de la mesa -->
                    <div class="mb-4">
                        @if($table->status == 'available')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-300 border border-green-500/50">
                                <i class="fas fa-check-circle mr-1"></i>
                                Disponible
                            </span>
                        @elseif($table->status == 'occupied')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-300 border border-red-500/50">
                                <i class="fas fa-users mr-1"></i>
                                Ocupada
                            </span>
                        @elseif($table->status == 'reserved')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-500/20 text-yellow-300 border border-yellow-500/50">
                                <i class="fas fa-calendar-check mr-1"></i>
                                Reservada
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-300 border border-gray-500/50">
                                <i class="fas fa-wrench mr-1"></i>
                                Mantenimiento
                            </span>
                        @endif
                    </div>

                    <!-- Información de la mesa -->
                    <div class="bg-white/5 rounded-lg p-3 mb-4 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">
                                <i class="fas fa-user-friends mr-1"></i>Capacidad:
                            </span>
                            <span class="text-white font-semibold">{{ $table->capacity }} personas</span>
                        </div>
                        @if($table->location)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-sm">
                                    <i class="fas fa-map-marker-alt mr-1"></i>Ubicación:
                                </span>
                                <span class="text-white font-semibold">{{ $table->location }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">
                                <i class="fas fa-toggle-on mr-1"></i>Estado:
                            </span>
                            <span class="font-semibold {{ $table->is_available ? 'text-green-400' : 'text-red-400' }}">
                                {{ $table->is_available ? 'Habilitada' : 'Deshabilitada' }}
                            </span>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex gap-2">
                        <a href="{{ route('tables.show', $table) }}"
                           class="flex-1 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 text-center py-2 px-3 rounded-lg text-sm font-medium transition-all">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('tables.edit', $table) }}"
                           class="flex-1 bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 text-center py-2 px-3 rounded-lg text-sm font-medium transition-all">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('tables.destroy', $table) }}" method="POST" class="flex-1"
                              onsubmit="return confirm('¿Estás seguro de eliminar esta mesa?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full bg-red-500/20 hover:bg-red-500/30 text-red-300 py-2 px-3 rounded-lg text-sm font-medium transition-all">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-cyan-400/20 to-blue-500/20 mb-4">
                        <i class="fas fa-table text-4xl text-cyan-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">No hay mesas registradas</h3>
                    <p class="text-gray-400 mb-6">Agrega tu primera mesa para comenzar</p>
                    <a href="{{ route('tables.create') }}"
                       class="ocean-btn inline-flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        Nueva Mesa
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Paginación con estilo Ocean -->
        @if($tables->hasPages())
            <div class="mt-8 ocean-card p-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-400">
                        Mostrando {{ $tables->firstItem() }} - {{ $tables->lastItem() }} de {{ $tables->total() }} mesas
                    </div>
                    <div class="pagination-ocean">
                        {{ $tables->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('styles')
    <style>
        /* Estilos para la paginación Ocean */
        .pagination-ocean nav {
            display: flex;
            gap: 0.5rem;
        }

        .pagination-ocean nav > div {
            display: flex;
            gap: 0.5rem;
        }

        .pagination-ocean a,
        .pagination-ocean span {
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.5rem;
        }

        .pagination-ocean a {
            background: rgba(255, 255, 255, 0.1);
            color: #06b6d4;
            border: 1px solid rgba(6, 182, 212, 0.3);
        }

        .pagination-ocean a:hover {
            background: rgba(6, 182, 212, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);
        }

        .pagination-ocean span[aria-current="page"] {
            background: linear-gradient(135deg, #06b6d4, #0ea5e9);
            color: white;
            border: 1px solid rgba(6, 182, 212, 0.5);
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
        }

        .pagination-ocean span[aria-disabled="true"] {
            background: rgba(255, 255, 255, 0.05);
            color: #6b7280;
            cursor: not-allowed;
            opacity: 0.5;
        }

        /* Estilos para los selects */
        select option {
            background: #1e293b;
            color: white;
        }
    </style>
    @endpush
</x-app-layout>
