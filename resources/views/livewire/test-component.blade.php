<div class="p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Test de Creación de Pedidos</h2>

    <div class="mb-6">
        <button wire:click="createTestOrder"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            🧪 Crear Pedido de Prueba
        </button>

        <button wire:click="loadOrders"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded ml-2">
            🔄 Recargar Pedidos
        </button>
    </div>

    @if($testResult)
        <div class="mb-4 p-4 border rounded-lg {{ str_contains($testResult, '✅') ? 'bg-green-100 border-green-400 text-green-700' : (str_contains($testResult, '⚠️') ? 'bg-yellow-100 border-yellow-400 text-yellow-700' : 'bg-red-100 border-red-400 text-red-700') }}">
            <strong>Resultado de la Prueba:</strong><br>
            {{ $testResult }}
        </div>
    @endif

    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">
            📋 Pedidos del Usuario Actual ({{ Auth::user()->name ?? 'No autenticado' }})
        </h3>
        <p class="text-sm text-gray-600 mb-3">Total de pedidos: {{ count($orders) }}</p>

        @if(count($orders) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 border-b text-left">ID</th>
                            <th class="py-2 px-4 border-b text-left">Mesa</th>
                            <th class="py-2 px-4 border-b text-left">Cliente</th>
                            <th class="py-2 px-4 border-b text-left">Estado</th>
                            <th class="py-2 px-4 border-b text-left">Total</th>
                            <th class="py-2 px-4 border-b text-left">Productos</th>
                            <th class="py-2 px-4 border-b text-left">Creado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-4 border-b">#{{ $order->id }}</td>
                                <td class="py-2 px-4 border-b">{{ $order->table->name ?? 'N/A' }}</td>
                                <td class="py-2 px-4 border-b">{{ $order->customer_name ?? 'Sin nombre' }}</td>
                                <td class="py-2 px-4 border-b">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                           ($order->status === 'in_process' ? 'bg-blue-100 text-blue-800' :
                                           ($order->status === 'served' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="py-2 px-4 border-b">${{ number_format($order->total, 2) }}</td>
                                <td class="py-2 px-4 border-b">{{ count($order->items) }} items</td>
                                <td class="py-2 px-4 border-b">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <p>No hay pedidos para mostrar</p>
                <p class="text-sm">Crea un pedido de prueba usando el botón de arriba</p>
            </div>
        @endif
    </div>

    <div class="mt-6 p-4 bg-gray-100 rounded-lg">
        <h4 class="font-semibold text-gray-700 mb-2">ℹ️ Información de Debug:</h4>
        <p class="text-sm text-gray-600">Usuario autenticado: {{ Auth::check() ? 'Sí (' . Auth::user()->email . ')' : 'No' }}</p>
        <p class="text-sm text-gray-600">Rol del usuario: {{ Auth::user()->role ?? 'N/A' }}</p>
        <p class="text-sm text-gray-600">Mesas disponibles: {{ App\Models\Table::where('status', 'available')->count() }}</p>
        <p class="text-sm text-gray-600">Productos disponibles: {{ App\Models\Product::where('available', true)->count() }}</p>
    </div>
</div>
