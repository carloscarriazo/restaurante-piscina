<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Blue Lagoon</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <style>
        /* Paleta de colores Blue Lagoon */
        :root {
            --ocean-primary: #0ea5e9;       /* Sky-500 */
            --ocean-primary-dark: #0284c7;  /* Sky-600 */
            --ocean-secondary: #06b6d4;     /* Cyan-500 */
            --ocean-secondary-dark: #0891b2; /* Cyan-600 */
            --ocean-accent: #10b981;        /* Emerald-500 */
            --ocean-accent-dark: #059669;   /* Emerald-600 */
            --ocean-surface: rgba(255, 255, 255, 0.1);
            --ocean-surface-hover: rgba(255, 255, 255, 0.2);
            --ocean-text: #ffffff;
            --ocean-text-secondary: rgba(255, 255, 255, 0.8);
            --ocean-shadow: rgba(14, 165, 233, 0.3);
        }

        .ocean-wave {
            background: linear-gradient(45deg, rgba(14, 165, 233, 0.1) 25%, transparent 25%, transparent 75%, rgba(14, 165, 233, 0.1) 75%),
            linear-gradient(-45deg, rgba(14, 165, 233, 0.1) 25%, transparent 25%, transparent 75%, rgba(14, 165, 233, 0.1) 75%);
            background-size: 20px 20px;
            animation: wave-motion 20s linear infinite;
        }

        @keyframes wave-motion {
            0% { background-position: 0 0, 0 0; }
            100% { background-position: 20px 20px, -20px -20px; }
        }

        .glass-morphism {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        /* Estilos para navegación */
        .nav-link {
            @apply flex items-center space-x-2 px-4 py-2 rounded-xl font-medium text-sm transition-all duration-300;
            color: var(--ocean-text-secondary);
            background: transparent;
        }

        .nav-link:hover {
            color: var(--ocean-text);
            background: var(--ocean-surface-hover);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px var(--ocean-shadow);
        }

        .nav-link.active {
            color: var(--ocean-text);
            background: var(--ocean-surface-hover);
            box-shadow: 0 4px 15px var(--ocean-shadow);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .ocean-button {
            background: linear-gradient(135deg, var(--ocean-primary), var(--ocean-secondary));
            color: var(--ocean-text);
            transition: all 0.3s ease;
        }

        .ocean-button:hover {
            background: linear-gradient(135deg, var(--ocean-primary-dark), var(--ocean-secondary-dark));
            transform: translateY(-2px);
            box-shadow: 0 10px 30px var(--ocean-shadow);
        }

        .ocean-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            transition: all 0.3s ease;
        }

        .ocean-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-4px);
            box-shadow: 0 20px 40px var(--ocean-shadow);
        }

        .nav-icon {
            @apply text-lg;
        }

        .nav-text {
            @apply hidden sm:block font-medium;
        }

        /* Efectos adicionales */
        .nav-link.active .nav-icon {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0,0,0);
            }
            40%, 43% {
                transform: translate3d(0, -8px, 0);
            }
            70% {
                transform: translate3d(0, -4px, 0);
            }
            90% {
                transform: translate3d(0, -2px, 0);
            }
        }

        /* Menú móvil */
        .mobile-nav-link {
            @apply flex items-center space-x-4 w-full px-4 py-3 rounded-xl text-white/90 hover:text-white hover:bg-white/15 transition-all duration-300 font-medium;
        }

        .mobile-nav-link.active {
            @apply bg-white/25 text-white shadow-lg;
        }

        /* Responsive design improvements */
        @media (max-width: 640px) {
            .nav-text {
                @apply hidden;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-ocean-950 via-ocean-900 to-blue-950 ocean-wave">
    <x-banner />

    <div class="min-h-screen">
        <!-- Navegación Oceánica Mejorada -->
        <nav class="bg-gradient-to-r from-blue-900/95 via-blue-800/95 to-blue-700/95 backdrop-blur-xl border-b border-blue-300/30 shadow-2xl sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">

                    <!-- Logo Blue Lagoon - Mejorado -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('dashboard') }}" class="group flex items-center space-x-4 hover:scale-105 transition-transform duration-300">
                            <div class="relative">
                                <div class="w-14 h-14 bg-gradient-to-br from-cyan-400 via-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-xl border-2 border-white/30">
                                    <span class="text-white font-bold text-2xl group-hover:animate-bounce">🏊‍♂️</span>
                                </div>
                                <div class="absolute -top-1 -right-1 w-4 h-4 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full animate-pulse"></div>
                            </div>
                            <div class="hidden sm:block">
                                <h1 class="text-2xl font-bold text-white tracking-wide bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">
                                    Blue Lagoon
                                </h1>
                                <p class="text-sm text-cyan-200 font-medium">Restaurante & Piscina Resort</p>
                            </div>
                        </a>
                    </div>

                    <!-- Navegación Central - Organizada por Secciones -->
                    <div class="hidden lg:flex items-center space-x-1">

                        <!-- Sección: General -->
                        <div class="flex items-center space-x-1 px-4 py-2 rounded-xl bg-white/5">
                            <a href="{{ route('dashboard') }}"
                               class="nav-link group {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <span class="nav-icon">🏠</span>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </div>

                        <!-- Sección: Operaciones -->
                        @if(auth()->check() && (auth()->user()->hasPermission('Ver Órdenes') || auth()->user()->hasPermission('Ver Productos')))
                        <div class="flex items-center space-x-1 px-4 py-2 rounded-xl bg-white/5">
                            @if(auth()->user()->hasPermission('Ver Órdenes'))
                            <a href="/pedidos"
                               class="nav-link group {{ request()->is('pedidos') ? 'active' : '' }}">
                                <span class="nav-icon">📋</span>
                                <span class="nav-text">Pedidos</span>
                            </a>
                            @endif

                            @if(auth()->user()->hasPermission('Ver Productos'))
                            <a href="{{ route('products.index') }}"
                               class="nav-link group {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                <span class="nav-icon">🍽️</span>
                                <span class="nav-text">Productos</span>
                            </a>
                            @endif
                        </div>
                        @endif

                        <!-- Sección: Cocina -->
                        @if(auth()->check() && auth()->user()->hasPermission('Ver Cocina'))
                        <div class="flex items-center space-x-1 px-4 py-2 rounded-xl bg-white/5">
                            <a href="{{ route('kitchen.dashboard') }}"
                               class="nav-link group {{ request()->routeIs('kitchen.*') ? 'active' : '' }}">
                                <span class="nav-icon">👨‍🍳</span>
                                <span class="nav-text">Cocina</span>
                            </a>
                        </div>
                        @endif

                        <!-- Sección: Gestión -->
                        @if(auth()->check() && (auth()->user()->hasPermission('Ver Inventario') || auth()->user()->hasPermission('Ver Usuarios')))
                        <div class="flex items-center space-x-1 px-4 py-2 rounded-xl bg-white/5">
                            @if(auth()->user()->hasPermission('Ver Inventario'))
                            <a href="{{ route('inventory.index') }}"
                               class="nav-link group {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                                <span class="nav-icon">📦</span>
                                <span class="nav-text">Inventario</span>
                            </a>
                            @endif

                            <a href="/categories"
                               class="nav-link group {{ request()->is('categories') ? 'active' : '' }}">
                                <span class="nav-icon">🏷️</span>
                                <span class="nav-text">Categorías</span>
                            </a>

                            @if(auth()->user()->hasPermission('Ver Usuarios'))
                            <a href="{{ route('users.index') }}"
                               class="nav-link group {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <span class="nav-icon">👥</span>
                                <span class="nav-text">Usuarios</span>
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Sección Derecha: Usuario y Notificaciones -->
                    <div class="flex items-center space-x-4">
                        @auth
                        <!-- Notificaciones (placeholder) -->
                        <div class="relative">
                            <button class="p-2 text-white hover:bg-white/10 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                            </button>
                        </div>

                        <!-- Información del Usuario -->
                        <div class="hidden md:flex flex-col items-end text-right">
                            <div class="font-semibold text-white text-sm">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-cyan-200">
                                @foreach(Auth::user()->roles as $role)
                                    <span class="inline-block bg-blue-500/30 px-2 py-0.5 rounded-full mr-1">{{ $role->nombre }}</span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Avatar y Dropdown del Usuario -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center space-x-2 p-1 rounded-full hover:bg-white/10 transition-all duration-200 border-2 border-transparent hover:border-white/20">
                                <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-full flex items-center justify-center shadow-lg border border-white/30">
                                    <span class="text-white font-bold text-sm">{{ substr(Auth::user()->name, 0, 2) }}</span>
                                </div>
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Mejorado -->
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-64 bg-white/95 backdrop-blur-md rounded-xl shadow-2xl border border-white/20 z-50 overflow-hidden">

                                <!-- Header del dropdown -->
                                <div class="px-4 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white">
                                    <div class="font-medium">{{ Auth::user()->name }}</div>
                                    <div class="text-sm opacity-90">{{ Auth::user()->email }}</div>
                                </div>

                                <!-- Opciones del menú -->
                                <div class="py-2">
                                    <a href="{{ route('profile.show') }}"
                                       class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Mi Perfil
                                    </a>

                                    <a href="#" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Configuración
                                    </a>

                                    <div class="border-t border-gray-200 my-1"></div>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Cerrar Sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Menú móvil (hamburguesa) -->
                        <div class="lg:hidden" x-data="{ mobileOpen: false }">
                            <button @click="mobileOpen = !mobileOpen"
                                    class="p-2 rounded-md text-white hover:bg-white/10 transition-colors">
                                <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                                <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>

                            <!-- Menú móvil expandido -->
                            <div x-show="mobileOpen"
                                 @click.away="mobileOpen = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 class="absolute top-full left-0 right-0 bg-blue-900/98 backdrop-blur-xl border-t border-blue-300/30 shadow-2xl z-40">
                                <div class="px-4 py-6 space-y-4">

                                    <a href="{{ route('dashboard') }}"
                                       class="mobile-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                        <span class="text-xl">🏠</span>
                                        <span>Dashboard</span>
                                    </a>

                                    @if(auth()->check() && auth()->user()->hasPermission('Ver Órdenes'))
                                    <a href="/pedidos"
                                       class="mobile-nav-link {{ request()->is('pedidos') ? 'active' : '' }}">
                                        <span class="text-xl">📋</span>
                                        <span>Pedidos</span>
                                    </a>
                                    @endif

                                    @if(auth()->check() && auth()->user()->hasPermission('Ver Productos'))
                                    <a href="{{ route('products.index') }}"
                                       class="mobile-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                        <span class="text-xl">🍽️</span>
                                        <span>Productos</span>
                                    </a>
                                    @endif

                                    @if(auth()->check() && auth()->user()->hasPermission('Ver Cocina'))
                                    <a href="{{ route('kitchen.dashboard') }}"
                                       class="mobile-nav-link {{ request()->routeIs('kitchen.*') ? 'active' : '' }}">
                                        <span class="text-xl">👨‍🍳</span>
                                        <span>Cocina</span>
                                    </a>
                                    @endif

                                    @if(auth()->check() && auth()->user()->hasPermission('Ver Inventario'))
                                    <a href="{{ route('inventory.index') }}"
                                       class="mobile-nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                                        <span class="text-xl">📦</span>
                                        <span>Inventario</span>
                                    </a>
                                    @endif

                                    <a href="/categories"
                                       class="mobile-nav-link {{ request()->is('categories') ? 'active' : '' }}">
                                        <span class="text-xl">🏷️</span>
                                        <span>Categorías</span>
                                    </a>

                                    @if(auth()->check() && auth()->user()->hasPermission('Ver Usuarios'))
                                    <a href="{{ route('users.index') }}"
                                       class="mobile-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                        <span class="text-xl">👥</span>
                                        <span>Usuarios</span>
                                    </a>
                                    @endif

                                    <!-- Logout para móvil -->
                                    <div class="border-t border-white/20 mt-4 pt-4">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                    class="mobile-nav-link text-red-300 hover:text-red-100 hover:bg-red-500/20">
                                                <span class="text-xl">🚪</span>
                                                <span>Cerrar Sesión</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Contenido Principal -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    @livewireScripts

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
