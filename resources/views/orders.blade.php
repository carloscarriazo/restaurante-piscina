<x-app-layout>
    <x-slot name="title">Gestión de Pedidos</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header con estilo Ocean -->
        <div class="ocean-card p-6 mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        <i class="fas fa-shopping-cart mr-3 text-cyan-400"></i>
                        Gestión de Pedidos
                    </h1>
                    <p class="text-gray-300">Administra y rastrea todos los pedidos del restaurante</p>
                </div>
            </div>
        </div>

        <!-- Livewire Component -->
        @livewire('order-manager')
    </div>
</x-app-layout>
