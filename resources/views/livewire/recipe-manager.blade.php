<div class="min-h-screen bg-gradient-to-br from-blue-900 via-teal-800 to-cyan-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold text-white mb-2 drop-shadow-lg">
                🏊‍♀️ Gestión de Recetas - Blue Lagoon 👨‍🍳
            </h1>
            <p class="text-blue-200 text-lg">Configuración de ingredientes por platillo</p>
        </div>

        <!-- Controles Superiores -->
        <div class="mb-8 bg-white/10 backdrop-blur-md rounded-2xl p-6 shadow-2xl border border-white/20">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <!-- Buscador -->
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live="search" type="text"
                               class="block w-full pl-10 pr-4 py-3 border border-blue-300/30 rounded-xl bg-white/10 backdrop-blur text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                               placeholder="Buscar productos...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Productos -->
        <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-blue-600/50 to-teal-600/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Ingredientes</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($products as $product)
                            @php
                                $prepStatus = $this->canPrepareProduct($product);
                            @endphp
                            <tr class="hover:bg-white/5 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-white font-medium">{{ $product->name }}</div>
                                        @if($product->description)
                                            <div class="text-blue-200 text-sm">{{ Str::limit($product->description, 50) }}</div>
                                        @endif
                                        <div class="text-blue-300 text-xs">${{ number_format($product->price, 2) }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        @if($product->recipes->count() > 0)
                                            @foreach($product->recipes->take(3) as $recipe)
                                                <div class="text-sm text-gray-300">
                                                    <span class="text-white">{{ $recipe->ingredient->name }}</span>
                                                    <span class="text-blue-300">{{ $recipe->quantity }} {{ $recipe->ingredient->unit ?? '' }}</span>
                                                </div>
                                            @endforeach
                                            @if($product->recipes->count() > 3)
                                                <div class="text-xs text-gray-400">
                                                    +{{ $product->recipes->count() - 3 }} más...
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-yellow-400 text-sm">Sin receta configurada</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($product->recipes->count() === 0)
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-300">
                                            Sin receta
                                        </span>
                                    @elseif($prepStatus['can_prepare'])
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-500/20 text-green-300">
                                            ✓ Disponible
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-500/20 text-red-300">
                                            ⚠ Sin stock
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <!-- Botón Configurar Receta -->
                                        <button wire:click="openRecipeModal({{ $product->id }})"
                                                class="bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 p-2 rounded-lg transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                            </svg>
                                        </button>

                                        @if(!$prepStatus['can_prepare'] && count($prepStatus['missing_ingredients']) > 0)
                                            <div class="group relative">
                                                <button class="bg-red-500/20 text-red-300 p-2 rounded-lg cursor-help">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </button>
                                                <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 bg-slate-800 text-white text-xs rounded-lg p-2 w-64 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                                    <div class="font-medium mb-2">Ingredientes faltantes:</div>
                                                    @foreach($prepStatus['missing_ingredients'] as $missing)
                                                        <div class="text-red-300">
                                                            • {{ $missing['name'] }}: necesita {{ $missing['required'] }} {{ $missing['unit'] ?? '' }}, disponible {{ $missing['available'] }} {{ $missing['unit'] ?? '' }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="text-blue-200">
                                        <svg class="mx-auto h-12 w-12 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                        <h3 class="mt-2 text-lg font-medium">No hay productos</h3>
                                        <p class="mt-1">Registre productos para configurar sus recetas.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($products->hasPages())
                <div class="px-6 py-4 border-t border-white/10">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

        <!-- Modal de Receta -->
        @if($showModal && $modalType === 'recipe' && $selectedProduct)
            <div class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.8);">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-8 max-w-4xl w-full mx-auto shadow-2xl border border-slate-700">
                        <!-- Encabezado del Modal -->
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-white">Configurar Receta</h2>
                                <p class="text-gray-300">{{ $selectedProduct->name }}</p>
                            </div>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Agregar Ingrediente -->
                        <div class="mb-6 p-6 bg-slate-800/50 rounded-xl border border-slate-600">
                            <h3 class="text-lg font-semibold text-white mb-4">Agregar Ingrediente</h3>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label for="new_ingredient_id" class="block text-sm font-medium text-gray-300 mb-2">Ingrediente</label>
                                    <select id="new_ingredient_id" wire:model="newIngredientId" class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Seleccionar...</option>
                                        @foreach($ingredients as $ingredient)
                                            <option value="{{ $ingredient->id }}">{{ $ingredient->name }} ({{ $ingredient->unit ?? 'Sin unidad' }})</option>
                                        @endforeach
                                    </select>
                                    @error('newIngredientId') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="new_quantity" class="block text-sm font-medium text-gray-300 mb-2">Cantidad</label>
                                    <input id="new_quantity" wire:model="newQuantity" type="number" step="0.001"
                                           class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="0.000">
                                    @error('newQuantity') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex items-end">
                                    <button wire:click="addIngredient"
                                            class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white px-4 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-colors duration-200">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Agregar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de Ingredientes -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-white mb-4">Ingredientes de la Receta</h3>
                            @if(count($recipe_ingredients) > 0)
                                <div class="space-y-3">
                                    @foreach($recipe_ingredients as $index => $ingredient)
                                        <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-600">
                                            <div class="grid grid-cols-4 gap-4 items-center">
                                                <div>
                                                    <div class="text-white font-medium">{{ $ingredient['ingredient_name'] }}</div>
                                                    <div class="text-gray-400 text-sm">{{ $ingredient['ingredient_unit'] }}</div>
                                                </div>
                                                <div>
                                                    <input wire:model.live="recipe_ingredients.{{ $index }}.quantity"
                                                           type="number" step="0.001" min="0.001"
                                                           class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                </div>
                                                <div>
                                                    <input wire:model.live="recipe_ingredients.{{ $index }}.notes"
                                                           type="text" placeholder="Notas (opcional)"
                                                           class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                </div>
                                                <div class="flex justify-end">
                                                    <button wire:click="removeIngredient({{ $index }})"
                                                            class="bg-red-500/20 hover:bg-red-500/30 text-red-300 p-2 rounded transition-colors duration-200">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-400">
                                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    <p>No hay ingredientes configurados</p>
                                    <p class="text-sm">Agregue ingredientes para completar la receta</p>
                                </div>
                            @endif
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex justify-end space-x-4">
                            <button wire:click="closeModal"
                                    class="px-6 py-3 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                                Cancelar
                            </button>
                            <button wire:click="saveRecipe"
                                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-colors duration-200">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Guardar Receta
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Mensajes Flash -->
    @if(session()->has('message'))
        <div class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-xl">
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-xl">
            {{ session('error') }}
        </div>
    @endif
</div>
