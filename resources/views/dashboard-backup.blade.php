@php
    // Configuración de módulos del sistema
    $modules = [
        [
            'name' => 'Gestión de Pedidos',
            'description' => 'Crear, editar y gestionar pedidos de clientes',
            'url' => '/pedidos',
            'icon' => 'fas fa-shopping-cart',
            'color' => 'from-sky-400 to-cyan-500',
            'permission' => 'Ver Órdenes'
        ],
        [
            'name' => 'Panel de Cocina',
            'description' => 'Vista en tiempo real de pedidos para el equipo de cocina',
            'url' => '/kitchen/dashboard',
            'icon' => 'fas fa-utensils',
            'color' => 'from-cyan-400 to-teal-500',
            'permission' => 'Ver Cocina'
        ],
        [
            'name' => 'Gestión de Productos',
            'description' => 'Catálogo de productos y precios',
            'url' => '/productos',
            'icon' => 'fas fa-hamburger',
            'color' => 'from-teal-400 to-emerald-500',
            'permission' => 'Ver Productos'
        ],
        [
            'name' => 'Gestión de Menú Digital',
            'description' => 'Administrar menú digital disponible Vie-Dom',
            'url' => '/menu/manage',
            'icon' => 'fas fa-book-open',
            'color' => 'from-purple-400 to-pink-500',
            'permission' => 'Ver Productos'
        ],
        [
            'name' => 'Control de Inventario',
            'description' => 'Gestión de ingredientes y stock',
            'url' => '/inventario',
            'icon' => 'fas fa-boxes',
            'color' => 'from-emerald-400 to-sky-500',
            'permission' => 'Ver Inventario'
        ],
        [
            'name' => 'Recetas y Preparaciones',
            'description' => 'Gestión de recetas e ingredientes',
            'url' => '/recetas',
            'icon' => 'fas fa-book-open',
            'color' => 'from-cyan-500 to-blue-500',
            'permission' => 'Ver Inventario'
        ],
        [
            'name' => 'Gestión de Usuarios',
            'description' => 'Administración de usuarios y permisos',
            'url' => '/usuarios',
            'icon' => 'fas fa-users',
            'color' => 'from-sky-500 to-cyan-600',
            'permission' => 'Ver Usuarios'
        ]
    ];

    // Filtrar módulos según permisos del usuario
    $availableModules = array_filter($modules, function($module) {
        return auth()->user()->hasPermission($module['permission']);
    });
@endphp

<x-ocean-layout>
    <div class="min-h-screen bg-gradient-to-br from-cyan-50 via-blue-50 to-indigo-100 p-6">
        <!-- Header de Bienvenida -->
        <div class="max-w-7xl mx-auto mb-8">
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl border border-white/50 shadow-xl p-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-teal-600 to-blue-600 bg-clip-text text-transparent mb-2">
                        Bienvenido a Blue Lagoon
                    </h1>
                    <p class="text-xl text-gray-600 mb-4">Hola, {{ auth()->user()->name }}</p>
                    <div class="flex justify-center items-center space-x-6 text-sm text-gray-600">
                        <div class="flex items-center">
                            <i class="fas fa-user-tag mr-2" style="color: var(--ocean-primary)"></i>
                            {{ auth()->user()->roles->pluck('nombre')->join(', ') }}
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-calendar-day mr-2" style="color: var(--ocean-secondary)"></i>
                            {{ now()->format('d M Y') }}
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-clock mr-2" style="color: var(--ocean-accent)"></i>
                            {{ now()->format('H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Módulos -->
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($availableModules as $module)
                <div class="group relative">
                    <div class="absolute -inset-1 bg-gradient-to-r {{ $module['color'] }} rounded-2xl blur opacity-25 group-hover:opacity-75 transition duration-300"></div>
                    <div class="relative bg-white/80 backdrop-blur-sm rounded-2xl border border-white/50 shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform group-hover:-translate-y-2">
                        <!-- Icono -->
                        <div class="mb-4">
                            <div class="w-16 h-16 bg-gradient-to-r {{ $module['color'] }} rounded-xl flex items-center justify-center shadow-lg">
                                <i class="{{ $module['icon'] }} text-white text-2xl"></i>
                            </div>
                        </div>

                        <!-- Contenido -->
                        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $module['name'] }}</h3>
                        <p class="text-gray-600 mb-6 text-sm leading-relaxed">{{ $module['description'] }}</p>

                        <!-- Botón de Acceso -->
                        <a href="{{ $module['url'] }}"
                           class="inline-flex items-center justify-center w-full px-6 py-3 bg-gradient-to-r {{ $module['color'] }} text-white font-semibold rounded-xl hover:shadow-lg transform transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            <span>Acceder</span>
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-200"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Estadísticas Rápidas (Solo para usuarios con permisos administrativos) -->
        @if(auth()->user()->hasPermission('Ver Reportes'))
        <div class="max-w-7xl mx-auto mt-8">
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl border border-white/50 shadow-xl p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Estadísticas del Día</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center p-4 bg-gradient-to-r from-teal-400 to-teal-600 rounded-xl text-white">
                        <i class="fas fa-shopping-cart text-3xl mb-2"></i>
                        <div class="text-2xl font-bold">24</div>
                        <div class="text-sm opacity-90">Pedidos Hoy</div>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-r from-blue-400 to-blue-600 rounded-xl text-white">
                        <i class="fas fa-clock text-3xl mb-2"></i>
                        <div class="text-2xl font-bold">8</div>
                        <div class="text-sm opacity-90">En Preparación</div>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-r from-green-400 to-green-600 rounded-xl text-white">
                        <i class="fas fa-dollar-sign text-3xl mb-2"></i>
                        <div class="text-2xl font-bold">$1,250</div>
                        <div class="text-sm opacity-90">Ventas del Día</div>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-r from-purple-400 to-purple-600 rounded-xl text-white">
                        <i class="fas fa-users text-3xl mb-2"></i>
                        <div class="text-2xl font-bold">42</div>
                        <div class="text-sm opacity-90">Clientes Atendidos</div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-ocean-layout>
