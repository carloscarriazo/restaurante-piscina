<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles del Producto') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('products.edit', $product) }}"
                   class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('products.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Columna 1: Información básica -->
                        <div class="space-y-6">
                            <!-- Imagen del producto -->
                            <div>
                                @if($product->image_url)
                                    <img src="{{ asset('storage/' . $product->image_url) }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-64 object-cover rounded-lg shadow-md">
                                @else
                                    <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Estado del producto -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Estado del Producto</h3>
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        @if($product->available)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Disponible
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                </svg>
                                                No disponible
                                            </span>
                                        @endif
                                    </div>

                                    @if($product->active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                            Inactivo
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Información de stock -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Información de Inventario</h3>
                                <dl class="grid grid-cols-2 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Stock Actual</dt>
                                        <dd class="mt-1 text-xl font-semibold text-gray-900">
                                            {{ $product->stock ?? 0 }}
                                            @if($product->unit)
                                                <span class="text-sm text-gray-500">{{ $product->unit->abreviacion }}</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Stock Mínimo</dt>
                                        <dd class="mt-1 text-xl font-semibold {{ ($product->stock ?? 0) <= ($product->stock_minimo ?? 0) ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $product->stock_minimo ?? 0 }}
                                            @if($product->unit)
                                                <span class="text-sm text-gray-500">{{ $product->unit->abreviacion }}</span>
                                            @endif
                                        </dd>
                                    </div>
                                </dl>

                                @if(($product->stock ?? 0) <= ($product->stock_minimo ?? 0))
                                    <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-md">
                                        <div class="flex">
                                            <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-red-800">Stock bajo</h3>
                                                <p class="text-sm text-red-700 mt-1">
                                                    El stock actual está por debajo del mínimo recomendado.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Columna 2: Detalles del producto -->
                        <div class="space-y-6">
                            <!-- Información básica -->
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                                <p class="text-xl font-semibold text-green-600 mb-4">${{ number_format($product->price, 2) }}</p>

                                @if($product->description)
                                    <div class="mb-6">
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Descripción</h3>
                                        <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Detalles del producto -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Producto</h3>
                                <dl class="grid grid-cols-1 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Categoría</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $product->category->nombre }}
                                            </span>
                                        </dd>
                                    </div>

                                    @if($product->unit)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Unidad de Medida</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->unit->nombre }} ({{ $product->unit->abreviacion }})</dd>
                                    </div>
                                    @endif

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Creado</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('d/m/Y H:i') }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Última actualización</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->updated_at->format('d/m/Y H:i') }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Acciones rápidas -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones Rápidas</h3>
                                <div class="flex space-x-3">
                                    <form action="{{ route('products.toggle-availability', $product) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md {{ $product->available ? 'text-yellow-700 bg-yellow-100 hover:bg-yellow-200' : 'text-green-700 bg-green-100 hover:bg-green-200' }}">
                                            @if($product->available)
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                </svg>
                                                Marcar No Disponible
                                            @else
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Marcar Disponible
                                            @endif
                                        </button>
                                    </form>

                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline"
                                          onsubmit="return confirm('¿Está seguro de que desea eliminar este producto?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Eliminar Producto
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
