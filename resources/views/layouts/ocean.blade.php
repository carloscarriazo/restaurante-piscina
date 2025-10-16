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
        /* Efectos oceánicos personalizados */
        .ocean-wave {
            background: linear-gradient(45deg,
                rgba(59, 130, 246, 0.1) 25%,
                transparent 25%,
                transparent 75%,
                rgba(59, 130, 246, 0.1) 75%),
            linear-gradient(-45deg,
                rgba(59, 130, 246, 0.1) 25%,
                transparent 25%,
                transparent 75%,
                rgba(59, 130, 246, 0.1) 75%);
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
    </style>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600 ocean-wave">
    <x-banner />

    <div class="min-h-screen">
        <!-- Navegación Oceánica Unificada -->
        <nav class="bg-white/10 backdrop-blur-md border-b border-white/20 glass-morphism">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo y navegación principal -->
                    <div class="flex items-center space-x-6">
                        <!-- Logo Blue Lagoon -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                                    <span class="text-white font-bold text-xl">🏊‍♂️</span>
                                </div>
                                <div>
                                    <h1 class="text-xl font-bold text-white tracking-wide">Blue Lagoon</h1>
                                    <p class="text-xs text-blue-100">Restaurante & Piscina</p>
                                </div>
                            </a>
                        </div>

                        <!-- Navegación Principal -->
                        <div class="hidden md:flex space-x-1">
                            <a href="{{ route('dashboard') }}"
                               class="px-4 py-2 rounded-lg text-white hover:bg-white/20 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/30' : '' }}">
                                🏠 Inicio
                            </a>

                            @if(auth()->check() && auth()->user()->hasPermission('products.view'))
                            <a href="{{ route('products.index') }}"
                               class="px-4 py-2 rounded-lg text-white hover:bg-white/20 transition-all duration-200 {{ request()->routeIs('products.*') ? 'bg-white/30' : '' }}">
                                🍽️ Productos
                            </a>
                            @endif

                            @if(auth()->check() && auth()->user()->hasPermission('orders.view'))
                            <a href="/pedidos"
                               class="px-4 py-2 rounded-lg text-white hover:bg-white/20 transition-all duration-200 {{ request()->is('pedidos') ? 'bg-white/30' : '' }}">
                                📝 Pedidos
                            </a>
                            @endif

                            @if(auth()->check() && auth()->user()->hasPermission('kitchen.view'))
                            <a href="{{ route('kitchen.dashboard') }}"
                               class="px-4 py-2 rounded-lg text-white hover:bg-white/20 transition-all duration-200 {{ request()->routeIs('kitchen.*') ? 'bg-white/30' : '' }}">
                                👨‍🍳 Cocina
                            </a>
                            @endif

                            @if(auth()->check() && auth()->user()->hasPermission('inventory.view'))
                            <a href="{{ route('inventory.index') }}"
                               class="px-4 py-2 rounded-lg text-white hover:bg-white/20 transition-all duration-200 {{ request()->routeIs('inventory.*') ? 'bg-white/30' : '' }}">
                                📦 Inventario
                            </a>
                            @endif

                            @if(auth()->check() && auth()->user()->hasPermission('users.view'))
                            <a href="{{ route('users.index') }}"
                               class="px-4 py-2 rounded-lg text-white hover:bg-white/20 transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-white/30' : '' }}">
                                👥 Usuarios
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Usuario actual y opciones -->
                    <div class="flex items-center space-x-4">
                        @auth
                        <!-- Nombre del usuario -->
                        <div class="text-white text-sm">
                            <div class="font-medium">{{ Auth::user()->name }}</div>
                            <div class="text-blue-200 text-xs">{{ Auth::user()->roles->pluck('nombre')->implode(', ') }}</div>
                        </div>

                        <!-- Dropdown del usuario -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center space-x-2 px-3 py-2 rounded-lg text-white hover:bg-white/20 transition-all duration-200">
                                <div class="w-8 h-8 bg-white/30 rounded-full flex items-center justify-center">
                                    <span class="text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>

                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50">
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Perfil</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Cerrar Sesión
                                    </button>
                                </form>
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
