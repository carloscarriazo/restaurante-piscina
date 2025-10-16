<div class="space-y-6">
    <!-- Test de Livewire -->
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
        <p><strong>Debug Info:</strong></p>
        <p>ShowModal: {{ $showModal ? 'true' : 'false' }}</p>
        <p>Orders count: {{ count($orders) }}</p>
        <p>Available tables: {{ count($availableTables) }}</p>
        <p>Products count: {{ count($products) }}</p>
    </div>

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

    <!-- Header con botón nuevo pedido -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Pedidos</h1>
            <p class="mt-2 text-sm text-gray-700">Administra los pedidos de tus mesas</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button wire:click="createOrder"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nuevo Pedido
            </button>
        </div>
    </div>

    <!-- Test Modal Simple -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 z-50">
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="bg-white rounded-lg p-6 max-w-md w-full">
                        <h3 class="text-lg font-medium mb-4">Modal de Prueba</h3>
                        <p>¡El modal funciona correctamente!</p>
                        <div class="mt-4">
                            <button wire:click="$set('showModal', false)"
                                    class="px-4 py-2 bg-gray-500 text-white rounded">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
