<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido | Restaurante Piscina</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fuente moderna -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-sky-600 via-teal-500 to-emerald-400 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-white/10 backdrop-blur-sm shadow-md fixed w-full top-0 z-50 border-b border-white/20">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <h1 class="text-2xl font-extrabold tracking-tight">🌊 Restaurante Piscina</h1>
            <div class="space-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-md transition shadow-md">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="px-4 py-2 bg-yellow-300 text-gray-900 rounded-md hover:bg-yellow-400 transition shadow-md">
                            Comenzar Ahora
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="flex-1 flex items-center justify-center pt-28">
        <div class="max-w-7xl mx-auto px-6 flex flex-col lg:flex-row items-center justify-center gap-12 w-full">

            <!-- Texto -->
            <section class="text-center lg:text-left max-w-xl space-y-8">
                <h2 class="text-4xl md:text-5xl font-extrabold drop-shadow-md text-yellow-200">
                    Bienvenido al sistema de gestión
                </h2>
                <p class="text-lg md:text-xl text-white/90">
                    Administra tu restaurante con un sistema rápido, moderno y visualmente atractivo. Control total desde pedidos hasta cocina y caja.
                </p>
                <div class="flex flex-wrap justify-center lg:justify-start gap-4 mt-8">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="px-6 py-3 bg-pink-600 hover:bg-pink-700 rounded-md shadow-lg transition">
                                Ir al Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="px-6 py-3 bg-white text-pink-700 hover:bg-pink-100 rounded-md shadow-lg transition">
                                Comenzar Ahora
                            </a>
                        @endauth
                    @endif
                </div>
            </section>

            {{-- <!-- Imagen decorativa -->
            <section class="hidden lg:block">
                <img src="https://source.unsplash.com/600x400/?restaurant,pool"
                     alt="Restaurante con piscina"
                     class="rounded-xl shadow-2xl border-4 border-white/30 hover:scale-105 transition duration-500">
            </section> --}}
        </div>
    </main>
</body>
</html>
