<div class="space-y-6">
    <!-- Mensajes de alerta -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header con botón crear -->
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Productos') }}
        </h2>
        <button wire:click="create"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nuevo Producto
        </button>
    </div>

    <!-- Filtros -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Buscar</label>
                    <input wire:model.live="search" type="text" id="search"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="Nombre del producto">
                </div>
                <div>
                    <label for="categoryFilter" class="block text-sm font-medium text-gray-700">Categoría</label>
                    <select wire:model.live="categoryFilter" id="categoryFilter"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="availableFilter" class="block text-sm font-medium text-gray-700">Estado</label>
                    <select wire:model.live="availableFilter" id="availableFilter"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todos los estados</option>
                        <option value="1">Disponible</option>
                        <option value="0">No disponible</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button wire:click="loadData(app(\App\Services\ProductService::class))"
                            class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de productos -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Producto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Categoría
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Precio
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stock
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($product->image_url)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->name }}">
                                        @else
                                            <div class="h-10 w-10 bg-gray-300 rounded-full flex items-center justify-center">
                                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                            <div class="text-sm text-gray-500">{{ Str::limit($product->description, 50) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $product->category->nombre }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($product->price, 2) }}
                                    @if($product->unit)
                                        <span class="text-gray-500">/ {{ $product->unit->abreviacion }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex flex-col">
                                        <span class="{{ ($product->stock ?? 0) <= ($product->stock_minimo ?? 0) ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                            {{ $product->stock ?? 0 }}
                                        </span>
                                        <span class="text-xs text-gray-500">Min: {{ $product->stock_minimo ?? 0 }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col space-y-1">
                                        @if($product->available)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Disponible
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                No disponible
                                            </span>
                                        @endif
                                        @if($product->active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Inactivo
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button wire:click="edit({{ $product->id }})"
                                               class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button wire:click="toggleAvailability({{ $product->id }})"
                                                class="{{ $product->available ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }}"
                                                title="{{ $product->available ? 'Marcar no disponible' : 'Marcar disponible' }}">
                                            @if($product->available)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @endif
                                        </button>
                                        <button wire:click="delete({{ $product->id }})"
                                                wire:confirm="¿Está seguro de que desea eliminar este producto?"
                                                class="text-red-600 hover:text-red-900" title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No hay productos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para crear/editar producto -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Header del modal -->
                    <div class="flex justify-between items-center pb-3">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $editing ? 'Editar Producto' : 'Nuevo Producto' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Formulario -->
                    <form wire:submit="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Columna 1 -->
                            <div class="space-y-4">
                                <!-- Nombre -->
                                <div>
                                    <label for="form.name" class="block text-sm font-medium text-gray-700">
                                        Nombre del Producto <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="form.name" type="text" id="form.name"
                                           class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('form.name') ? 'border-red-500' : 'border-gray-300' }}"
                                           placeholder="Ingrese el nombre del producto">
                                    @error('form.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Descripción -->
                                <div>
                                    <label for="form.description" class="block text-sm font-medium text-gray-700">
                                        Descripción
                                    </label>
                                    <textarea wire:model="form.description" id="form.description" rows="3"
                                              class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('form.description') ? 'border-red-500' : 'border-gray-300' }}"
                                              placeholder="Ingrese una descripción del producto"></textarea>
                                    @error('form.description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Precio -->
                                <div>
                                    <label for="form.price" class="block text-sm font-medium text-gray-700">
                                        Precio <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="form.price" type="number" step="0.01" min="0" id="form.price"
                                           class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('form.price') ? 'border-red-500' : 'border-gray-300' }}"
                                           placeholder="0.00">
                                    @error('form.price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Categoría -->
                                <div>
                                    <label for="form.category_id" class="block text-sm font-medium text-gray-700">
                                        Categoría <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex space-x-2">
                                        <select wire:model="form.category_id" id="form.category_id"
                                                class="mt-1 flex-1 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('form.category_id') ? 'border-red-500' : 'border-gray-300' }}">
                                            <option value="">Seleccione una categoría</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" wire:click="openCategoryModal"
                                                class="mt-1 px-3 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                                title="Crear nueva categoría">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    @error('form.category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Columna 2 -->
                            <div class="space-y-4">
                                <!-- Unidad -->
                                <div>
                                    <label for="form.unit_id" class="block text-sm font-medium text-gray-700">
                                        Unidad de Medida <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="form.unit_id" id="form.unit_id"
                                            class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('form.unit_id') ? 'border-red-500' : 'border-gray-300' }}">
                                        <option value="">Seleccione una unidad</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->nombre }} ({{ $unit->abreviacion }})</option>
                                        @endforeach
                                    </select>
                                    @error('form.unit_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Stock actual -->
                                <div>
                                    <label for="form.stock" class="block text-sm font-medium text-gray-700">
                                        Stock Actual
                                    </label>
                                    <input wire:model="form.stock" type="number" min="0" id="form.stock"
                                           class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('form.stock') ? 'border-red-500' : 'border-gray-300' }}"
                                           placeholder="0">
                                    @error('form.stock') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Stock mínimo -->
                                <div>
                                    <label for="form.stock_minimo" class="block text-sm font-medium text-gray-700">
                                        Stock Mínimo
                                    </label>
                                    <input wire:model="form.stock_minimo" type="number" min="0" id="form.stock_minimo"
                                           class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('form.stock_minimo') ? 'border-red-500' : 'border-gray-300' }}"
                                           placeholder="0">
                                    @error('form.stock_minimo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Imagen -->
                                <div>
                                    <label for="form.image" class="block text-sm font-medium text-gray-700">
                                        Imagen del Producto
                                    </label>

                                    <!-- Previsualización de imagen existente (solo al editar) -->
                                    @if($editing && isset($product) && $product->getImageUrl())
                                        <div class="mt-2 mb-3">
                                            <img src="{{ $product->getImageUrl() }}" alt="Producto actual" class="h-20 w-20 object-cover rounded-lg border">
                                            <p class="text-xs text-gray-500 mt-1">Imagen actual</p>
                                        </div>
                                    @endif

                                    <!-- Input de archivo -->
                                    <input wire:model="form.image" type="file" accept="image/*" id="form.image"
                                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('form.image') border-red-500 @enderror">

                                    <!-- Previsualización de nueva imagen -->
                                    @if($form['image'] ?? false)
                                        <div class="mt-2">
                                            <div class="text-xs text-gray-600">Nueva imagen seleccionada: {{ $form['image']->getClientOriginalName() }}</div>
                                        </div>
                                    @endif

                                    <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF hasta 2MB</p>
                                    @error('form.image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Estados -->
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <input wire:model="form.available" type="checkbox" id="form.available"
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="form.available" class="ml-2 block text-sm text-gray-900">
                                            Producto disponible
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input wire:model="form.active" type="checkbox" id="form.active"
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="form.active" class="ml-2 block text-sm text-gray-900">
                                            Producto activo
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones del modal -->
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ $editing ? 'Actualizar' : 'Guardar' }} Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal para crear nueva categoría -->
    @if($showCategoryModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Header del modal -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Nueva Categoría</h3>
                        <button wire:click="closeCategoryModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Formulario de categoría -->
                    <form wire:submit.prevent="createCategory">
                        <div class="space-y-4">
                            <!-- Nombre de la categoría -->
                            <div>
                                <label for="newCategoryName" class="block text-sm font-medium text-gray-700">
                                    Nombre <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="newCategoryName" type="text" id="newCategoryName"
                                       class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('newCategoryName') ? 'border-red-500' : 'border-gray-300' }}"
                                       placeholder="Nombre de la categoría">
                                @error('newCategoryName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Descripción -->
                            <div>
                                <label for="newCategoryDescription" class="block text-sm font-medium text-gray-700">
                                    Descripción
                                </label>
                                <textarea wire:model="newCategoryDescription" id="newCategoryDescription" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                          placeholder="Descripción de la categoría (opcional)"></textarea>
                                @error('newCategoryDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Botones del modal -->
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeCategoryModal"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Crear Categoría
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
