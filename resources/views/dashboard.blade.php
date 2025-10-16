@php
    // Configuración de módulos del sistema
    $modules = [
        [
            'name' => 'Gestión de Pedidos',
            'description' => 'Crear, editar y gestionar pedidos de clientes',
            'url' => '/pedidos',
            'icon' => 'fas fa-shopping-cart',
            'color' => 'from-cyan-400 to-blue-500',
            'stats' => '24 hoy',
            'permission' => 'Ver Órdenes'
        ],
        [
            'name' => 'Panel de Cocina',
            'description' => 'Vista en tiempo real de pedidos para el equipo de cocina',
            'url' => route('kitchen.dashboard'),
            'icon' => 'fas fa-fire',
            'color' => 'from-orange-400 to-red-500',
            'stats' => '8 activos',
            'permission' => 'Ver Cocina'
        ],
        [
            'name' => 'Menú Digital',
            'description' => 'Administrar menú digital disponible Vie-Dom',
            'url' => route('menu.manage'),
            'icon' => 'fas fa-utensils',
            'color' => 'from-purple-400 to-pink-500',
            'stats' => '79 items',
            'permission' => 'Ver Productos'
        ],
        [
            'name' => 'Productos',
            'description' => 'Catálogo de productos y precios',
            'url' => route('products.index'),
            'icon' => 'fas fa-box',
            'color' => 'from-green-400 to-teal-500',
            'stats' => '156 productos',
            'permission' => 'Ver Productos'
        ],
        [
            'name' => 'Categorías',
            'description' => 'Organizar productos por categorías',
            'url' => route('categories.index'),
            'icon' => 'fas fa-tags',
            'color' => 'from-yellow-400 to-orange-500',
            'stats' => '19 categorías',
            'permission' => 'Ver Productos'
        ],
        [
            'name' => 'Mesas',
            'description' => 'Gestión y asignación de mesas',
            'url' => route('tables.index'),
            'icon' => 'fas fa-chair',
            'color' => 'from-blue-400 to-indigo-500',
            'stats' => '15 mesas',
            'permission' => 'Ver Mesas'
        ],
        [
            'name' => 'Usuarios',
            'description' => 'Administración de usuarios y permisos',
            'url' => '/usuarios',
            'icon' => 'fas fa-users',
            'color' => 'from-indigo-400 to-purple-500',
            'stats' => '8 usuarios',
            'permission' => 'Ver Usuarios'
        ],
        [
            'name' => 'Reportes',
            'description' => 'Estadísticas y análisis de ventas',
            'url' => '/reportes',
            'icon' => 'fas fa-chart-line',
            'color' => 'from-pink-400 to-rose-500',
            'stats' => 'Ver datos',
            'permission' => 'Ver Reportes'
        ]
    ];

    // Filtrar módulos según permisos del usuario
    $availableModules = array_filter($modules, function($module) {
        return auth()->user()->hasPermission($module['permission']);
    });
@endphp

<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <style>
        /* Cards Ocean */
        .ocean-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .ocean-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #06b6d4, #0ea5e9, #3b82f6);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .ocean-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(6, 182, 212, 0.3);
            background: rgba(255, 255, 255, 0.15);
        }

        .ocean-card:hover::before {
            opacity: 1;
        }

        .ocean-module-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1.25rem;
            padding: 2rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .ocean-module-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.1), rgba(14, 165, 233, 0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .ocean-module-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px rgba(6, 182, 212, 0.4);
            border-color: rgba(6, 182, 212, 0.5);
        }

        .ocean-module-card:hover::before {
            opacity: 1;
        }

        .ocean-module-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #06b6d4, #0ea5e9);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            box-shadow: 0 10px 25px rgba(6, 182, 212, 0.4);
            transition: all 0.3s ease;
        }

        .ocean-module-card:hover .ocean-module-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 15px 35px rgba(6, 182, 212, 0.6);
        }

        .ocean-btn {
            background: linear-gradient(135deg, #06b6d4, #0ea5e9);
            border: none;
            color: white;
            padding: 0.875rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);
        }

        .ocean-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(6, 182, 212, 0.5);
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
        }

        .ocean-stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .ocean-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(6, 182, 212, 0.3);
        }

        .ocean-stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #06b6d4, #0ea5e9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @media (max-width: 768px) {
            .ocean-module-card {
                padding: 1.5rem;
            }

            .ocean-module-icon {
                width: 60px;
                height: 60px;
                font-size: 1.75rem;
            }
        }
    </style>

    <!-- Header de Bienvenida -->
    <div class="ocean-card mb-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-3">
                <span style="background: linear-gradient(135deg, #06b6d4, #0ea5e9, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    ¡Bienvenido a Blue Lagoon!
                </span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-200 mb-4">Hola, <strong>{{ auth()->user()->name }}</strong></p>
            <div class="flex flex-wrap justify-center items-center gap-6 text-sm md:text-base text-gray-300">
                <div class="flex items-center gap-2">
                    <i class="fas fa-user-tag" style="color: #06b6d4;"></i>
                    <span>{{ auth()->user()->roles->pluck('nombre')->join(', ') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-calendar-day" style="color: #0ea5e9;"></i>
                    <span>{{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-clock" style="color: #3b82f6;"></i>
                    <span>{{ now()->format('H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    @if(auth()->user()->hasPermission('Ver Reportes'))
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="ocean-stat-card">
            <i class="fas fa-shopping-cart text-cyan-400 text-3xl mb-3"></i>
            <div class="ocean-stat-value">24</div>
            <div class="text-gray-300 font-medium">Pedidos Hoy</div>
        </div>
        <div class="ocean-stat-card">
            <i class="fas fa-fire text-orange-400 text-3xl mb-3"></i>
            <div class="ocean-stat-value">8</div>
            <div class="text-gray-300 font-medium">En Cocina</div>
        </div>
        <div class="ocean-stat-card">
            <i class="fas fa-dollar-sign text-green-400 text-3xl mb-3"></i>
            <div class="ocean-stat-value">$1.2M</div>
            <div class="text-gray-300 font-medium">Ventas Hoy</div>
        </div>
        <div class="ocean-stat-card">
            <i class="fas fa-users text-purple-400 text-3xl mb-3"></i>
            <div class="ocean-stat-value">42</div>
            <div class="text-gray-300 font-medium">Clientes</div>
        </div>
    </div>
    @endif

    <!-- Grid de Módulos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($availableModules as $module)
        <div class="ocean-module-card">
            <!-- Icono -->
            <div class="ocean-module-icon mb-4">
                <i class="{{ $module['icon'] }}"></i>
            </div>

            <!-- Contenido -->
            <div class="flex-grow">
                <h3 class="text-xl font-bold text-white mb-2">{{ $module['name'] }}</h3>
                <p class="text-gray-300 text-sm mb-3 leading-relaxed">{{ $module['description'] }}</p>

                @if(isset($module['stats']))
                <div class="inline-block px-3 py-1 bg-white/10 rounded-full text-xs text-cyan-300 font-medium mb-4">
                    <i class="fas fa-info-circle mr-1"></i>{{ $module['stats'] }}
                </div>
                @endif
            </div>

            <!-- Botón -->
            <a href="{{ $module['url'] }}" class="ocean-btn w-full justify-center">
                <span>Acceder</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        @endforeach
    </div>

    <!-- Accesos Rápidos Adicionales -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="ocean-card">
            <div class="flex items-center gap-4 mb-4">
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #06b6d4, #0ea5e9); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-qrcode text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Menú Digital QR</h3>
                    <p class="text-gray-300 text-sm">Vista pública del menú</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('menu.digital.preview') }}" target="_blank" class="ocean-btn flex-1 justify-center text-sm">
                    <i class="fas fa-eye"></i>
                    <span>Ver Menú</span>
                </a>
                <a href="{{ route('menu.qr') }}" class="ocean-btn flex-1 justify-center text-sm">
                    <i class="fas fa-qrcode"></i>
                    <span>Ver QR</span>
                </a>
            </div>
        </div>

        <div class="ocean-card">
            <div class="flex items-center gap-4 mb-4">
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 0.75rem; display: flex; align-items: center; justify-center;">
                    <i class="fas fa-cog text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Configuración</h3>
                    <p class="text-gray-300 text-sm">Ajustes del sistema</p>
                </div>
            </div>
            <a href="{{ route('profile.show') }}" class="ocean-btn w-full justify-center text-sm">
                <i class="fas fa-user-cog"></i>
                <span>Mi Perfil</span>
            </a>
        </div>
    </div>

</x-app-layout>
