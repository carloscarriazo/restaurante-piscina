<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-authenticated" content="true">
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="user-role" content="{{ auth()->user()->role }}">
    @endauth

    <title>{{ config('app.name', 'Blue Lagoon Restaurant') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/notifications.css', 'resources/js/notifications.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body class="font-sans antialiased">
    <x-banner />

    <div class="min-h-screen bg-gray-100">
        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- Contenedor de notificaciones toast -->
    <div class="notification-container" id="notification-container"></div>

    @stack('modals')

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
</body>

</html>
