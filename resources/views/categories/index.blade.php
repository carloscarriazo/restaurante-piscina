<x-app-layout>
    <x-slot name="title">Gestión de Categorías</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header con estilo Ocean -->
        <div class="ocean-card p-6 mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        <i class="fas fa-tags mr-3 text-cyan-400"></i>
                        Gestión de Categorías
                    </h1>
                    <p class="text-gray-300">Organiza tus productos por categorías</p>
                </div>
                <a href="{{ route('categories.create') }}"
                   class="ocean-btn inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Nueva Categoría
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
                        <p class="text-gray-300 text-sm">Total Categorías</p>
                        <p class="text-3xl font-bold text-white">{{ $categories->total() }}</p>
                    </div>
                    <i class="fas fa-tags text-4xl text-cyan-400"></i>
                </div>
            </div>
            <div class="ocean-card p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-300 text-sm">Activas</p>
                        <p class="text-3xl font-bold text-green-400">{{ $categories->where('is_active', true)->count() }}</p>
                    </div>
                    <i class="fas fa-check-circle text-4xl text-green-400"></i>
                </div>
            </div>
            <div class="ocean-card p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-300 text-sm">Inactivas</p>
                        <p class="text-3xl font-bold text-red-400">{{ $categories->where('is_active', false)->count() }}</p>
                    </div>
                    <i class="fas fa-times-circle text-4xl text-red-400"></i>
                </div>
            </div>
            <div class="ocean-card p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-300 text-sm">Con Productos</p>
                        <p class="text-3xl font-bold text-blue-400">{{ $categories->where('products_count', '>', 0)->count() }}</p>
                    </div>
                    <i class="fas fa-box text-4xl text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Tabla de Categorías con diseño Ocean -->
        <div class="ocean-card overflow-hidden">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-white/10">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-cyan-400 uppercase tracking-wider">
                                    <i class="fas fa-tag mr-2"></i>Nombre
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-cyan-400 uppercase tracking-wider">
                                    <i class="fas fa-align-left mr-2"></i>Descripción
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-cyan-400 uppercase tracking-wider">
                                    <i class="fas fa-box mr-2"></i>Productos
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-cyan-400 uppercase tracking-wider">
                                    <i class="fas fa-toggle-on mr-2"></i>Estado
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-cyan-400 uppercase tracking-wider">
                                    <i class="fas fa-cog mr-2"></i>Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($categories as $category)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center mr-3">
                                                <i class="fas fa-tag text-white"></i>
                                            </div>
                                            <span class="text-sm font-semibold text-white">{{ $category->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-300">{{ Str::limit($category->description, 50) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/50">
                                            <i class="fas fa-box mr-1"></i>
                                            {{ $category->products_count }} productos
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($category->is_active)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-300 border border-green-500/50">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Activa
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-300 border border-red-500/50">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                Inactiva
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('categories.show', $category) }}"
                                               class="bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 p-2 rounded-lg transition-all"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('categories.edit', $category) }}"
                                               class="bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 p-2 rounded-lg transition-all"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('categories.toggle-active', $category) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="{{ $category->is_active ? 'bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-300' : 'bg-green-500/20 hover:bg-green-500/30 text-green-300' }} p-2 rounded-lg transition-all"
                                                        title="{{ $category->is_active ? 'Desactivar' : 'Activar' }}">
                                                    <i class="fas fa-{{ $category->is_active ? 'toggle-off' : 'toggle-on' }}"></i>
                                                </button>
                                            </form>
                                            @if($category->products_count == 0)
                                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('¿Está seguro de que desea eliminar esta categoría?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="bg-red-500/20 hover:bg-red-500/30 text-red-300 p-2 rounded-lg transition-all"
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-cyan-400/20 to-blue-500/20 mb-4">
                                            <i class="fas fa-tags text-4xl text-cyan-400"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-white mb-2">No hay categorías registradas</h3>
                                        <p class="text-gray-400 mb-6">Comienza creando tu primera categoría</p>
                                        <a href="{{ route('categories.create') }}" class="ocean-btn inline-flex items-center gap-2">
                                            <i class="fas fa-plus"></i>
                                            Crear Primera Categoría
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación con estilo Ocean -->
                @if($categories->hasPages())
                    <div class="mt-6 pt-6 border-t border-white/10">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-400">
                                Mostrando {{ $categories->firstItem() }} - {{ $categories->lastItem() }} de {{ $categories->total() }} categorías
                            </div>
                            <div class="pagination-ocean">
                                {{ $categories->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
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
    </style>
    @endpush
</x-app-layout>
