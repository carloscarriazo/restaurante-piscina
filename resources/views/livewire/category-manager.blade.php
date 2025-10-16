<div class="space-y-6">
    <!-- Mensajes de alerta con estilo Ocean -->
    @if (session()->has('success'))
        <div class="bg-gradient-to-r from-green-400/20 to-emerald-500/20 border border-green-400/50 text-green-100 px-6 py-4 rounded-xl backdrop-blur-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-green-400"></i>
                {{ session('success') }}
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
        <div class="ocean-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Total Categorías</p>
                    <p class="text-3xl font-bold text-white">{{ $categories->count() }}</p>
                </div>
                <i class="fas fa-tags text-4xl text-cyan-400"></i>
            </div>
        </div>
        <div class="ocean-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Activas</p>
                    <p class="text-3xl font-bold text-green-400">{{ $categories->where('activo', true)->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-4xl text-green-400"></i>
            </div>
        </div>
        <div class="ocean-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Inactivas</p>
                    <p class="text-3xl font-bold text-red-400">{{ $categories->where('activo', false)->count() }}</p>
                </div>
                <i class="fas fa-times-circle text-4xl text-red-400"></i>
            </div>
        </div>
        <div class="ocean-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Productos Total</p>
                    <p class="text-3xl font-bold text-cyan-400">{{ $categories->sum('productos_count') }}</p>
                </div>
                <i class="fas fa-box text-4xl text-cyan-400"></i>
            </div>
        </div>
    </div>

    <!-- Barra de búsqueda y botón nueva categoría -->
    <div class="ocean-card p-4">
        <div class="flex flex-col sm:flex-row gap-4 items-center">
            <div class="relative flex-1 w-full">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text"
                       wire:model.live="search"
                       placeholder="Buscar categorías por nombre o descripción..."
                       class="w-full pl-12 pr-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all">
            </div>
            <button wire:click="createCategory" class="ocean-btn whitespace-nowrap w-full sm:w-auto">
                <i class="fas fa-plus mr-2"></i>
                Nueva Categoría
            </button>
        </div>
    </div>

    <!-- Grid de categorías -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($categories as $category)
            <div class="ocean-card hover:transform hover:scale-105 transition-all duration-300">
                <!-- Header de la card -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-white mb-2 flex items-center gap-2">
                            <i class="fas fa-tag text-cyan-400"></i>
                            {{ $category->nombre }}
                        </h3>
                        <p class="text-gray-300 text-sm">
                            {{ $category->descripcion ?? 'Sin descripción' }}
                        </p>
                    </div>
                    <div>
                        @if($category->activo)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                                <i class="fas fa-check-circle mr-1"></i>
                                Activa
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">
                                <i class="fas fa-times-circle mr-1"></i>
                                Inactiva
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Contador de productos -->
                <div class="bg-white/5 rounded-lg p-4 mb-4 border border-white/10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-box text-2xl text-white"></i>
                            </div>
                            <div>
                                <p class="text-gray-300 text-sm">Productos</p>
                                <p class="text-2xl font-bold text-white">{{ $category->productos_count ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="flex gap-2">
                    <button wire:click="editCategory({{ $category->id }})"
                            class="flex-1 bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300 flex items-center justify-center gap-2 border border-white/10">
                        <i class="fas fa-edit"></i>
                        <span>Editar</span>
                    </button>
                    <button wire:click="toggleActive({{ $category->id }})"
                            class="flex-1 {{ $category->activo ? 'bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-400 border border-yellow-500/30' : 'bg-green-500/20 hover:bg-green-500/30 text-green-400 border border-green-500/30' }} px-4 py-2 rounded-lg font-medium transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-{{ $category->activo ? 'eye-slash' : 'eye' }}"></i>
                        <span>{{ $category->activo ? 'Desactivar' : 'Activar' }}</span>
                    </button>
                    <button wire:click="deleteCategory({{ $category->id }})"
                            wire:confirm="¿Está seguro de eliminar esta categoría?"
                            class="bg-red-500/20 hover:bg-red-500/30 text-red-400 border border-red-500/30 px-4 py-2 rounded-lg font-medium transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="ocean-card text-center py-12">
                    <i class="fas fa-tags text-6xl text-cyan-400 mb-4 opacity-50"></i>
                    <h3 class="text-xl font-bold text-white mb-2">No hay categorías</h3>
                    <p class="text-gray-300 mb-6">
                        @if($search)
                            No se encontraron categorías que coincidan con "{{ $search }}"
                        @else
                            Comienza creando tu primera categoría de productos
                        @endif
                    </p>
                    <button wire:click="createCategory" class="ocean-btn">
                        <i class="fas fa-plus mr-2"></i>
                        Nueva Categoría
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Modal para crear/editar categoría -->
    @if($showCategoryForm)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
            <div class="relative w-full max-w-2xl">
                <div class="card-ocean">
                    <!-- Header del modal -->
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-{{ $isEditMode ? 'edit' : 'plus-circle' }} text-ocean-400"></i>
                            {{ $isEditMode ? 'Editar Categoría' : 'Nueva Categoría' }}
                        </h3>
                        <button wire:click="closeModal" class="text-ocean-400 hover:text-white transition-colors">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>

                    <!-- Formulario -->
                    <form wire:submit.prevent="saveCategory">
                        <div class="space-y-6">
                            <!-- Nombre -->
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-ocean-200 mb-2">
                                    <i class="fas fa-tag mr-1"></i>
                                    Nombre de la Categoría <span class="text-red-400">*</span>
                                </label>
                                <input wire:model="nombre" type="text" id="nombre" class="input-ocean w-full @error('nombre') border-red-500 @enderror" placeholder="Ej: Bebidas, Platos Principales, Postres">
                                @error('nombre')
                                    <span class="text-red-400 text-sm flex items-center gap-1 mt-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <!-- Descripción -->
                            <div>
                                <label for="descripcion" class="block text-sm font-medium text-ocean-200 mb-2">
                                    <i class="fas fa-align-left mr-1"></i>
                                    Descripción
                                </label>
                                <textarea wire:model="descripcion" id="descripcion" rows="3" class="input-ocean w-full resize-none @error('descripcion') border-red-500 @enderror" placeholder="Descripción opcional de la categoría"></textarea>
                                @error('descripcion')
                                    <span class="text-red-400 text-sm flex items-center gap-1 mt-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Botones del modal -->
                        <div class="mt-8 flex justify-end gap-3">
                            <button type="button" wire:click="closeModal" class="btn-ocean-secondary">
                                <i class="fas fa-times mr-2"></i>
                                Cancelar
                            </button>
                            <button type="submit" class="btn-ocean">
                                <i class="fas fa-save mr-2"></i>
                                {{ $isEditMode ? 'Actualizar' : 'Crear' }} Categoría
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
