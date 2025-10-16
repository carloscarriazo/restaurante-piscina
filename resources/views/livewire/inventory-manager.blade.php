<div class="min-h-screen bg-gradient-to-br from-blue-900 via-teal-800 to-cyan-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold text-white mb-2 drop-shadow-lg">
                🏊‍♀️ Gestión de Inventario - Blue Lagoon 🍽️
            </h1>
            <p class="text-blue-200 text-lg">Control completo de ingredientes y stock</p>
        </div>

        <!-- Alertas de Stock Bajo -->
        @if($lowStockIngredients > 0)
            <div class="mb-6 bg-gradient-to-r from-red-600 to-orange-500 rounded-xl p-4 text-white shadow-xl">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-bold">¡Alerta de Stock Bajo!</h3>
                        <p>{{ $lowStockIngredients }} ingredientes necesitan reposición urgente</p>
                    </div>
                </div>
            </div>
        @endif

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
                               placeholder="Buscar ingredientes...">
                    </div>
                </div>

                <!-- Botón Agregar -->
                <button wire:click="openAddModal"
                        class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold shadow-xl transition-all duration-300 transform hover:scale-105">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nuevo Ingrediente
                </button>
            </div>
        </div>

        <!-- Tabla de Ingredientes -->
        <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-blue-600/50 to-teal-600/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Ingrediente</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Stock Actual</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Stock Mínimo</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Costo</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($ingredients as $ingredient)
                            <tr class="hover:bg-white/5 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-white font-medium">{{ $ingredient->name }}</div>
                                        @if($ingredient->description)
                                            <div class="text-blue-200 text-sm">{{ $ingredient->description }}</div>
                                        @endif
                                        <div class="text-blue-300 text-xs">{{ $ingredient->unit ?? 'Sin unidad' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <span class="text-lg font-bold {{ $ingredient->current_stock <= $ingredient->minimum_stock ? 'text-red-400' : 'text-green-400' }}">
                                            {{ number_format($ingredient->current_stock, 1) }}
                                        </span>
                                        @if($ingredient->current_stock <= $ingredient->minimum_stock)
                                            <svg class="w-5 h-5 text-red-400 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-yellow-400 font-medium">{{ number_format($ingredient->minimum_stock, 1) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-green-300 font-medium">
                                        @if($ingredient->cost_per_unit)
                                            ${{ number_format($ingredient->cost_per_unit, 2) }}
                                        @else
                                            <span class="text-gray-400">No definido</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $ingredient->is_active ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300' }}">
                                        {{ $ingredient->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <!-- Botón Editar -->
                                        <button wire:click="openEditModal({{ $ingredient->id }})"
                                                class="bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 p-2 rounded-lg transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>

                                        <!-- Botón Movimiento -->
                                        <button wire:click="openMovementModal({{ $ingredient->id }})"
                                                class="bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 p-2 rounded-lg transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-blue-200">
                                        <svg class="mx-auto h-12 w-12 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8V3.5m0 0L12 2m0 1.5L10 2"></path>
                                        </svg>
                                        <h3 class="mt-2 text-lg font-medium">No hay ingredientes</h3>
                                        <p class="mt-1">Comience agregando su primer ingrediente al inventario.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($ingredients->hasPages())
                <div class="px-6 py-4 border-t border-white/10">
                    {{ $ingredients->links() }}
                </div>
            @endif
        </div>

        <!-- Modal -->
        @if($showModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.8);">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-8 max-w-2xl w-full mx-auto shadow-2xl border border-slate-700">
                        <!-- Encabezado del Modal -->
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-white">
                                @if($modalType === 'add') Agregar Ingrediente
                                @elseif($modalType === 'edit') Editar Ingrediente
                                @elseif($modalType === 'movement') Movimiento de Stock
                                @endif
                            </h2>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        @if($modalType === 'movement')
                            <!-- Formulario de Movimiento -->
                            <div class="space-y-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="movement-quantity" class="block text-sm font-medium text-gray-300 mb-2">Cantidad</label>
                                        <input id="movement-quantity" wire:model="movementQuantity" type="number" step="0.001"
                                               class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="0.000">
                                        @error('movementQuantity') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="movementReason" class="block text-sm font-medium text-gray-300 mb-2">Razón del Movimiento</label>
                                    <select id="movementReason" wire:model="movementReason" class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Seleccionar razón...</option>
                                        <option value="purchase">Compra</option>
                                        <option value="waste">Desperdicio</option>
                                        <option value="adjustment">Ajuste de inventario</option>
                                        <option value="return">Devolución</option>
                                    </select>
                                    @error('movementReason') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex justify-end space-x-4">
                                    <button wire:click="addStock"
                                            class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-colors duration-200">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Agregar Stock
                                    </button>
                                    <button wire:click="subtractStock"
                                            class="bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-3 rounded-lg hover:from-red-700 hover:to-red-800 transition-colors duration-200">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                        Reducir Stock
                                    </button>
                                </div>
                            </div>
                        @else
                            <!-- Formulario de Ingrediente -->
                            <form wire:submit.prevent="saveIngredient">
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label for="ingredient-name" class="block text-sm font-medium text-gray-300 mb-2">Nombre *</label>
                                        <input id="ingredient-name"
                                               wire:model="name" type="text"
                                               class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="Nombre del ingrediente">
                                        @error('name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="ingredient-unit" class="block text-sm font-medium text-gray-300 mb-2">Unidad *</label>
                                        <select id="ingredient-unit" wire:model="unit" class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">Seleccionar unidad...</option>
                                            <option value="kg">Kilogramos (kg)</option>
                                            <option value="g">Gramos (g)</option>
                                            <option value="L">Litros (L)</option>
                                            <option value="ml">Mililitros (ml)</option>
                                            <option value="unidad">Unidad</option>
                                            <option value="piezas">Piezas</option>
                                            <option value="tazas">Tazas</option>
                                            <option value="cucharadas">Cucharadas</option>
                                            <option value="cucharaditas">Cucharaditas</option>
                                        </select>
                                        @error('unit') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-span-2">
                                        <label for="ingredient-description" class="block text-sm font-medium text-gray-300 mb-2">Descripción</label>
                                        <textarea id="ingredient-description"
                                                  wire:model="description"
                                                  class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                  placeholder="Descripción del ingrediente"></textarea>
                                        @error('description') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="ingredient-current-stock" class="block text-sm font-medium text-gray-300 mb-2">Stock Actual *</label>
                                        <input id="ingredient-current-stock"
                                               wire:model="currentStock" type="number" step="0.001"
                                               class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="0.000">
                                        @error('currentStock') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="ingredient-minimum-stock" class="block text-sm font-medium text-gray-300 mb-2">Stock Mínimo *</label>
                                        <input id="ingredient-minimum-stock"
                                               wire:model="minimumStock" type="number" step="0.001"
                                               class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="0.000">
                                        @error('minimumStock') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="ingredient-cost-per-unit" class="block text-sm font-medium text-gray-300 mb-2">Costo por Unidad</label>
                                        <input id="ingredient-cost-per-unit"
                                               wire:model="costPerUnit" type="number" step="0.01"
                                               class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="0.00">
                                        @error('costPerUnit') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="ingredient-supplier" class="block text-sm font-medium text-gray-300 mb-2">Proveedor</label>
                                        <input id="ingredient-supplier"
                                               wire:model="supplier" type="text"
                                               class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="Nombre del proveedor">
                                        @error('supplier') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-span-2">
                                        <label class="flex items-center">
                                            <input wire:model="isActive" type="checkbox" class="rounded bg-slate-700 border-slate-600 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-300">Ingrediente activo</span>
                                        </label>
                                        @error('isActive') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-4 mt-8">
                                    <button type="button" wire:click="closeModal"
                                            class="px-6 py-3 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                            class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-colors duration-200">
                                        {{ $modalType === 'add' ? 'Crear' : 'Actualizar' }} Ingrediente
                                    </button>
                                </div>
                            </form>
                        @endif
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
