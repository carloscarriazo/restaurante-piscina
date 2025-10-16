<x-app-layout>
    <x-slot name="title">Gestión de Categorías</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header con estilo Ocean -->
        <div class="ocean-card p-6 mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        <i class="fas fa-tags mr-3 text-cyan-400"></i>
                        Gestión de Categorías
                    </h1>
                    <p class="text-gray-300">Organiza y administra las categorías de productos</p>
                </div>
            </div>
        </div>

        <!-- Livewire Component -->
        @livewire('category-manager')
    </div>
</x-app-layout>
