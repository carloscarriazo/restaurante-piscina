<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center bg-gradient-to-r from-blue-600 via-cyan-600 to-teal-600 p-6 rounded-lg shadow-lg">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight">
                    ➕ {{ __('Nuevo Producto') }}
                </h2>
                <p class="text-blue-100 mt-1">Agrega un nuevo producto al menú del restaurante</p>
            </div>
            <a href="{{ route('products.index') }}"
               class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-3 px-6 rounded-full shadow-lg transform transition hover:scale-105 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Columna 1 -->
                            <div class="space-y-6">
                                <!-- Nombre -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">
                                        Nombre del Producto <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="name"
                                           id="name"
                                           value="{{ old('name') }}"
                                           class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}"
                                           placeholder="Ingrese el nombre del producto">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Descripción -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">
                                        Descripción
                                    </label>
                                    <textarea name="description"
                                              id="description"
                                              rows="4"
                                              class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('description') ? 'border-red-500' : 'border-gray-300' }}"
                                              placeholder="Ingrese una descripción del producto">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Precio -->
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700">
                                        Precio <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number"
                                           name="price"
                                           id="price"
                                           step="0.01"
                                           min="0"
                                           value="{{ old('price') }}"
                                           class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('price') ? 'border-red-500' : 'border-gray-300' }}"
                                           placeholder="0.00">
                                    @error('price')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Categoría -->
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700">
                                        Categoría <span class="text-red-500">*</span>
                                    </label>
                                    <select name="category_id"
                                            id="category_id"
                                            class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('category_id') ? 'border-red-500' : 'border-gray-300' }}">
                                        <option value="">Seleccione una categoría</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Columna 2 -->
                            <div class="space-y-6">
                                <!-- Unidad -->
                                <div>
                                    <label for="unit_id" class="block text-sm font-medium text-gray-700">
                                        Unidad de Medida <span class="text-red-500">*</span>
                                    </label>
                                    <select name="unit_id"
                                            id="unit_id"
                                            class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('unit_id') ? 'border-red-500' : 'border-gray-300' }}">
                                        <option value="">Seleccione una unidad</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Stock actual -->
                                <div>
                                    <label for="stock" class="block text-sm font-medium text-gray-700">
                                        Stock Actual
                                    </label>
                                    <input type="number"
                                           name="stock"
                                           id="stock"
                                           min="0"
                                           value="{{ old('stock') }}"
                                           class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('stock') ? 'border-red-500' : 'border-gray-300' }}"
                                           placeholder="0">
                                    @error('stock')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Stock mínimo -->
                                <div>
                                    <label for="stock_minimo" class="block text-sm font-medium text-gray-700">
                                        Stock Mínimo
                                    </label>
                                    <input type="number"
                                           name="stock_minimo"
                                           id="stock_minimo"
                                           min="0"
                                           value="{{ old('stock_minimo') }}"
                                           class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('stock_minimo') ? 'border-red-500' : 'border-gray-300' }}"
                                           placeholder="0">
                                    @error('stock_minimo')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Imagen -->
                                <div>
                                    <label for="image" class="block text-sm font-medium text-gray-700">
                                        Imagen del Producto
                                    </label>
                                    <input type="file"
                                           name="image"
                                           id="image"
                                           accept="image/*"
                                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('image') border-red-500 @enderror">
                                    <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF hasta 2MB</p>
                                    @error('image')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Estado -->
                                <div>
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                               name="available"
                                               id="available"
                                               value="1"
                                               {{ old('available', true) ? 'checked' : '' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="available" class="ml-2 block text-sm text-gray-900">
                                            Producto disponible
                                        </label>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Los productos disponibles aparecerán en el menú
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end space-x-4">
                            <a href="{{ route('products.index') }}"
                               class="bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white font-bold py-3 px-6 rounded-full shadow-lg transform transition hover:scale-105 inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-600 hover:to-cyan-600 text-white font-bold py-3 px-6 rounded-full shadow-lg transform transition hover:scale-105 inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Guardar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
