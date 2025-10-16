<div class="bg-white p-6 rounded-lg shadow-lg">
    <h3 class="text-lg font-semibold mb-4">Prueba de Componente Livewire</h3>
    <p class="text-gray-600">Si ves esto, Livewire está funcionando correctamente.</p>

    <div class="mt-4">
        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Botón de Prueba
        </button>
    </div>

    @if(!empty($products))
        <div class="mt-6">
            <h4 class="font-medium mb-2">Productos encontrados: {{ count($products) }}</h4>
            <div class="grid gap-2">
                @foreach($products as $product)
                    <div class="p-2 bg-gray-100 rounded">
                        {{ $product->name ?? 'Sin nombre' }} - ${{ $product->price ?? '0.00' }}
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="mt-6 p-4 bg-yellow-100 rounded">
            <p class="text-yellow-800">No se encontraron productos o la variable $products está vacía.</p>
        </div>
    @endif
</div>
