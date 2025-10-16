<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Blue Lagoon' }} - Restaurante & Piscina</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            background-attachment: fixed;
            min-height: 100vh;
            color: #e2e8f0;
        }

        /* Patrón de fondo Ocean */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(6, 182, 212, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(14, 165, 233, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(59, 130, 246, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        /* Variables CSS Ocean */
        :root {
            --ocean-primary: #06b6d4;
            --ocean-secondary: #0ea5e9;
            --ocean-accent: #3b82f6;
            --ocean-dark: #0f172a;
            --ocean-light: #e0f2fe;
            --ocean-gray: #334155;
            --ocean-success: #10b981;
            --ocean-warning: #f59e0b;
            --ocean-danger: #ef4444;
        }

        /* Navbar */
        .ocean-navbar {
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.95) 0%, rgba(14, 165, 233, 0.95) 100%);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(6, 182, 212, 0.3);
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .ocean-navbar-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
        }

        .ocean-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .ocean-logo:hover {
            transform: scale(1.05);
        }

        .ocean-logo i {
            font-size: 2rem;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.3));
        }

        /* Navegación Desktop */
        .ocean-nav-links {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .ocean-nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1rem;
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .ocean-nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .ocean-nav-link.active {
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .ocean-nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: white;
            border-radius: 3px 3px 0 0;
        }

        /* Usuario Dropdown */
        .ocean-user-menu {
            position: relative;
        }

        .ocean-user-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            color: white;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .ocean-user-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }

        .ocean-user-dropdown {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            min-width: 220px;
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .ocean-user-menu:hover .ocean-user-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .ocean-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.25rem;
            color: #334155;
            text-decoration: none;
            transition: all 0.2s ease;
            border-bottom: 1px solid #e2e8f0;
        }

        .ocean-dropdown-item:last-child {
            border-bottom: none;
        }

        .ocean-dropdown-item:hover {
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.1), rgba(14, 165, 233, 0.1));
            color: #06b6d4;
        }

        .ocean-dropdown-item i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        /* Mobile Menu Button */
        .ocean-mobile-menu-btn {
            display: none;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 1.5rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .ocean-mobile-menu-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Mobile Sidebar */
        .ocean-mobile-sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            width: 280px;
            height: 100vh;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.98), rgba(14, 165, 233, 0.98));
            backdrop-filter: blur(10px);
            box-shadow: 5px 0 30px rgba(0, 0, 0, 0.3);
            transition: left 0.4s ease;
            z-index: 2000;
            overflow-y: auto;
        }

        .ocean-mobile-sidebar.active {
            left: 0;
        }

        .ocean-sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }

        .ocean-sidebar-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 1.5rem;
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .ocean-sidebar-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .ocean-sidebar-links {
            padding: 1rem;
        }

        .ocean-sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .ocean-sidebar-link:hover,
        .ocean-sidebar-link.active {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }

        .ocean-sidebar-link i {
            font-size: 1.2rem;
            width: 24px;
        }

        /* Overlay */
        .ocean-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1999;
        }

        .ocean-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Contenedor Principal */
        .ocean-container {
            position: relative;
            z-index: 1;
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .ocean-nav-links {
                display: none;
            }

            .ocean-mobile-menu-btn {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .ocean-navbar-container {
                height: 60px;
            }

            .ocean-logo {
                font-size: 1.25rem;
            }

            .ocean-logo i {
                font-size: 1.5rem;
            }

            .ocean-container {
                padding: 1rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body x-data="{ mobileMenuOpen: false }">

    <!-- Navbar -->
    <nav class="ocean-navbar">
        <div class="ocean-navbar-container">
            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="ocean-logo">
                <i class="fas fa-water"></i>
                <span>Blue Lagoon</span>
            </a>

            <!-- Desktop Navigation -->
            <div class="ocean-nav-links">
                <a href="{{ route('dashboard') }}" class="ocean-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('menu.manage') }}" class="ocean-nav-link {{ request()->routeIs('menu.*') ? 'active' : '' }}">
                    <i class="fas fa-utensils"></i>
                    <span>Menú</span>
                </a>
                <a href="/pedidos" class="ocean-nav-link {{ request()->is('pedidos*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Órdenes</span>
                </a>
                <a href="{{ route('kitchen.dashboard') }}" class="ocean-nav-link {{ request()->routeIs('kitchen.*') ? 'active' : '' }}">
                    <i class="fas fa-fire"></i>
                    <span>Cocina</span>
                </a>
                <a href="{{ route('products.index') }}" class="ocean-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Productos</span>
                </a>
                <a href="{{ route('categories.index') }}" class="ocean-nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>Categorías</span>
                </a>
                <a href="{{ route('tables.index') }}" class="ocean-nav-link {{ request()->routeIs('tables.*') ? 'active' : '' }}">
                    <i class="fas fa-chair"></i>
                    <span>Mesas</span>
                </a>

                <!-- Usuario Menu -->
                <div class="ocean-user-menu">
                    <button class="ocean-user-button">
                        <i class="fas fa-user-circle"></i>
                        <span>{{ Auth::user()->name ?? 'Usuario' }}</span>
                        <i class="fas fa-chevron-down" style="font-size: 0.75rem;"></i>
                    </button>
                    <div class="ocean-user-dropdown">
                        <a href="{{ route('profile.show') }}" class="ocean-dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>Mi Perfil</span>
                        </a>
                        <a href="#" class="ocean-dropdown-item">
                            <i class="fas fa-cog"></i>
                            <span>Configuración</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="ocean-dropdown-item" style="width: 100%; border: none; background: none; cursor: pointer; text-align: left;">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Cerrar Sesión</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <button class="ocean-mobile-menu-btn" @click="mobileMenuOpen = true">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Mobile Sidebar -->
    <div class="ocean-mobile-sidebar" :class="{ 'active': mobileMenuOpen }">
        <div class="ocean-sidebar-header">
            <a href="{{ route('dashboard') }}" class="ocean-logo">
                <i class="fas fa-water"></i>
                <span>Blue Lagoon</span>
            </a>
            <button class="ocean-sidebar-close" @click="mobileMenuOpen = false">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="ocean-sidebar-links">
            <!-- Usuario Info -->
            <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 0.5rem; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-user-circle" style="font-size: 2.5rem;"></i>
                    <div>
                        <div style="font-weight: 600; font-size: 1.1rem;">{{ Auth::user()->name ?? 'Usuario' }}</div>
                        <div style="font-size: 0.85rem; opacity: 0.9;">{{ Auth::user()->email ?? '' }}</div>
                    </div>
                </div>
            </div>

            <a href="{{ route('dashboard') }}" class="ocean-sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" @click="mobileMenuOpen = false">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('menu.manage') }}" class="ocean-sidebar-link {{ request()->routeIs('menu.*') ? 'active' : '' }}" @click="mobileMenuOpen = false">
                <i class="fas fa-utensils"></i>
                <span>Menú</span>
            </a>
            <a href="/pedidos" class="ocean-sidebar-link {{ request()->is('pedidos*') ? 'active' : '' }}" @click="mobileMenuOpen = false">
                <i class="fas fa-shopping-cart"></i>
                <span>Órdenes</span>
            </a>
            <a href="{{ route('kitchen.dashboard') }}" class="ocean-sidebar-link {{ request()->routeIs('kitchen.*') ? 'active' : '' }}" @click="mobileMenuOpen = false">
                <i class="fas fa-fire"></i>
                <span>Cocina</span>
            </a>
            <a href="{{ route('products.index') }}" class="ocean-sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}" @click="mobileMenuOpen = false">
                <i class="fas fa-box"></i>
                <span>Productos</span>
            </a>
            <a href="{{ route('categories.index') }}" class="ocean-sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" @click="mobileMenuOpen = false">
                <i class="fas fa-tags"></i>
                <span>Categorías</span>
            </a>
            <a href="{{ route('tables.index') }}" class="ocean-sidebar-link {{ request()->routeIs('tables.*') ? 'active' : '' }}" @click="mobileMenuOpen = false">
                <i class="fas fa-chair"></i>
                <span>Mesas</span>
            </a>

            <hr style="border: none; border-top: 1px solid rgba(255, 255, 255, 0.2); margin: 1rem 0;">

            <a href="{{ route('profile.show') }}" class="ocean-sidebar-link" @click="mobileMenuOpen = false">
                <i class="fas fa-user"></i>
                <span>Mi Perfil</span>
            </a>
            <a href="#" class="ocean-sidebar-link" @click="mobileMenuOpen = false">
                <i class="fas fa-cog"></i>
                <span>Configuración</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="ocean-sidebar-link" style="width: 100%; border: none; background: none; cursor: pointer; text-align: left;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Overlay -->
    <div class="ocean-overlay" :class="{ 'active': mobileMenuOpen }" @click="mobileMenuOpen = false"></div>

    <!-- Contenido Principal -->
    <main class="ocean-container">
        {{ $slot }}
    </main>

    @livewireScripts
    @stack('scripts')
</body>
</html>
