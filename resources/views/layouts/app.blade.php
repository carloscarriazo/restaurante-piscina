<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-authenticated" content="true">
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="user-role" content="{{ auth()->user()->role }}">
    @endauth

    <title>{{ $title ?? config('app.name', 'Restaurante Piscina') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles only -->
    @vite(['resources/css/app.css', 'resources/css/notifications.css'])

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Ocean Design System Styles -->
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
        }

        .ocean-navbar {
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.95), rgba(14, 165, 233, 0.95));
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(6, 182, 212, 0.3);
        }

        .ocean-sidebar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.5);
        }

        .ocean-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            transition: all 0.3s ease;
        }

        .ocean-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(6, 182, 212, 0.3);
        }

        .ocean-btn {
            background: linear-gradient(135deg, #06b6d4, #0ea5e9);
            color: white;
            font-weight: 600;
            padding: 0.625rem 1.25rem;
            border-radius: 0.75rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);
        }

        .ocean-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(6, 182, 212, 0.5);
        }

        .ocean-nav-link {
            color: white;
            text-decoration: none;
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .ocean-nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .ocean-nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 10px rgba(6, 182, 212, 0.3);
        }

        /* Estilos adicionales Ocean para componentes */
        .card-ocean {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 41, 59, 0.95));
            backdrop-filter: blur(10px);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .btn-ocean {
            background: linear-gradient(135deg, #06b6d4, #0ea5e9);
            color: white;
            font-weight: 600;
            padding: 0.625rem 1.25rem;
            border-radius: 0.75rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-ocean:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(6, 182, 212, 0.5);
        }

        .btn-ocean-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-weight: 600;
            padding: 0.625rem 1.25rem;
            border-radius: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-ocean-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .input-ocean {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.75rem;
            color: white;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .input-ocean:focus {
            outline: none;
            border-color: #06b6d4;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
        }

        .input-ocean::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .text-ocean-200 {
            color: rgba(255, 255, 255, 0.8);
        }

        .text-ocean-400 {
            color: #06b6d4;
        }
    </style>

    @stack('styles')
</head>

<body class="font-sans antialiased h-full">
    <div class="min-h-screen" x-data="{ sidebarOpen: false }">
        <!-- Navbar Desktop -->
        <nav class="ocean-navbar fixed top-0 left-0 right-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo y Nombre -->
                    <div class="flex items-center space-x-3">
                        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-white hover:bg-white/10 p-2 rounded-lg transition-colors">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <i class="fas fa-water text-2xl text-white"></i>
                        <span class="text-white font-bold text-xl hidden sm:inline">Restaurante Piscina</span>
                        <span class="text-white font-bold text-xl sm:hidden">PiscinaRest</span>
                    </div>

                    <!-- Navigation Links (Desktop) -->
                    <div class="hidden md:flex items-center space-x-2">
                        <a href="{{ route('dashboard') }}" class="ocean-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home mr-2"></i>Inicio
                        </a>
                        <a href="{{ route('menu.manage') }}" class="ocean-nav-link {{ request()->routeIs('menu.*') ? 'active' : '' }}">
                            <i class="fas fa-utensils mr-2"></i>Menú
                        </a>
                        <a href="/pedidos" class="ocean-nav-link {{ request()->is('pedidos*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart mr-2"></i>Órdenes
                        </a>
                        <a href="{{ route('kitchen.dashboard') }}" class="ocean-nav-link {{ request()->routeIs('kitchen.*') ? 'active' : '' }}">
                            <i class="fas fa-fire mr-2"></i>Cocina
                        </a>
                        <a href="{{ route('products.index') }}" class="ocean-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <i class="fas fa-box mr-2"></i>Productos
                        </a>
                        <a href="{{ route('categories.index') }}" class="ocean-nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <i class="fas fa-tags mr-2"></i>Categorías
                        </a>
                        <a href="{{ route('tables.index') }}" class="ocean-nav-link {{ request()->routeIs('tables.*') ? 'active' : '' }}">
                            <i class="fas fa-table mr-2"></i>Mesas
                        </a>
                    </div>

                    <!-- User Dropdown -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="text-white hover:bg-white/10 p-2 rounded-lg transition-colors relative">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                        </button>

                        <!-- User Menu -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-2 text-white hover:bg-white/10 px-3 py-2 rounded-lg transition-colors">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <span class="hidden sm:inline font-medium">{{ Auth::user()->name ?? 'Admin' }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-2 z-50"
                                 style="display: none;">
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-gray-700 hover:bg-cyan-50 transition-colors">
                                    <i class="fas fa-user-circle mr-2"></i>Mi Perfil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Mobile Sidebar -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
             style="display: none;"></div>

        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed top-0 left-0 h-full w-64 ocean-sidebar z-50 transform md:hidden"
             style="display: none;">

            <div class="p-4 border-b border-white/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-water text-xl text-cyan-400"></i>
                        <span class="text-white font-bold">Piscina Rest</span>
                    </div>
                    <button @click="sidebarOpen = false" class="text-white hover:bg-white/10 p-2 rounded-lg">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <nav class="p-4 space-y-2">
                <a href="{{ route('dashboard') }}" class="block text-white hover:bg-white/10 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-white/20' : '' }}">
                    <i class="fas fa-home mr-3"></i>Inicio
                </a>
                <a href="{{ route('menu.manage') }}" class="block text-white hover:bg-white/10 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('menu.*') ? 'bg-white/20' : '' }}">
                    <i class="fas fa-utensils mr-3"></i>Menú
                </a>
                <a href="/pedidos" class="block text-white hover:bg-white/10 px-4 py-3 rounded-lg transition-colors {{ request()->is('pedidos*') ? 'bg-white/20' : '' }}">
                    <i class="fas fa-shopping-cart mr-3"></i>Órdenes
                </a>
                <a href="{{ route('kitchen.dashboard') }}" class="block text-white hover:bg-white/10 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('kitchen.*') ? 'bg-white/20' : '' }}">
                    <i class="fas fa-fire mr-3"></i>Cocina
                </a>
                <a href="{{ route('products.index') }}" class="block text-white hover:bg-white/10 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('products.*') ? 'bg-white/20' : '' }}">
                    <i class="fas fa-box mr-3"></i>Productos
                </a>
                <a href="{{ route('categories.index') }}" class="block text-white hover:bg-white/10 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('categories.*') ? 'bg-white/20' : '' }}">
                    <i class="fas fa-tags mr-3"></i>Categorías
                </a>
                <a href="{{ route('tables.index') }}" class="block text-white hover:bg-white/10 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('tables.*') ? 'bg-white/20' : '' }}">
                    <i class="fas fa-table mr-3"></i>Mesas
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <main class="pt-16">
            <x-banner />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-gradient-to-r from-cyan-500/10 to-blue-500/10 backdrop-blur-sm border-b border-white/10">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <div class="py-6">
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- Contenedor de notificaciones toast -->
    <div class="notification-container" id="notification-container"></div>

    @stack('modals')

    <!-- 1. Application Scripts (incluye Alpine) - PRIMERO -->
    @vite(['resources/js/app.js', 'resources/js/notifications.js'])

    <!-- 2. Livewire Scripts - SEGUNDO -->
    @livewireScripts

    <!-- Script para inicializar las notificaciones -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar usuario actual para notificaciones
            @auth
                window.currentUserId = {{ auth()->id() }};
                window.currentUserRole = '{{ auth()->user()->role }}';
            @endauth

            // Inicializar conexión de notificaciones
            if (window.currentUserId) {
                console.log('🔄 Inicializando sistema de notificaciones...');
            }
        });

        // Manejar eventos de Livewire globalmente
        document.addEventListener('livewire:init', () => {
            Livewire.on('show-toast', (event) => {
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: event
                }));
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
