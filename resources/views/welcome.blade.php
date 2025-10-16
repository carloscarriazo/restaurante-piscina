<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido | Restaurante Piscina - Morroa, Sucre</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Ocean Background */
        .ocean-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            position: relative;
            overflow: hidden;
        }

        .ocean-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(6, 182, 212, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(14, 165, 233, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(59, 130, 246, 0.05) 0%, transparent 50%);
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        /* Brand Gradient */
        .brand-gradient {
            background: linear-gradient(135deg, #06b6d4 0%, #0ea5e9 50%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glassmorphism */
        .glass-card {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(6, 182, 212, 0.2);
            box-shadow: 0 0 40px rgba(6, 182, 212, 0.1);
        }

        /* Floating Animation */
        .floating {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        /* Particle Effect */
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(6, 182, 212, 0.3);
            pointer-events: none;
            animation: particleFloat 20s infinite;
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) translateX(0) rotate(0deg);
                opacity: 0;
            }
            10% { opacity: 0.3; }
            90% { opacity: 0.3; }
            100% {
                transform: translateY(-100vh) translateX(100px) rotate(360deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body class="ocean-bg min-h-screen">
    <!-- Particles -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="particle w-2 h-2" style="left: 10%; animation-delay: 0s;"></div>
        <div class="particle w-3 h-3" style="left: 30%; animation-delay: 3s;"></div>
        <div class="particle w-1 h-1" style="left: 50%; animation-delay: 6s;"></div>
        <div class="particle w-2 h-2" style="left: 70%; animation-delay: 9s;"></div>
        <div class="particle w-3 h-3" style="left: 90%; animation-delay: 12s;"></div>
    </div>

    <!-- Navbar -->
    <nav class="glass-card fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <i class="fas fa-swimming-pool text-4xl text-cyan-400"></i>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold brand-gradient">
                        Restaurante Piscina
                    </h1>
                    <span class="hidden md:inline text-sm text-gray-400">Morroa, Sucre</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('menu.index') }}"
                   class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-semibold rounded-lg transition shadow-lg shadow-cyan-500/50 flex items-center gap-2">
                    <i class="fas fa-utensils"></i>
                    <span class="hidden sm:inline">Ver Carta</span>
                </a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-semibold rounded-lg transition shadow-lg shadow-green-500/50 flex items-center gap-2">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="hidden sm:inline">Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="px-4 py-2 bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-white font-semibold rounded-lg transition shadow-lg shadow-yellow-500/50 flex items-center gap-2">
                            <i class="fas fa-sign-in-alt"></i>
                            <span class="hidden sm:inline">Ingresar</span>
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="relative z-10 flex items-center justify-center min-h-screen pt-20">
        <div class="max-w-7xl mx-auto px-6 flex flex-col lg:flex-row items-center justify-center gap-12 w-full py-12">
            <!-- Content -->
            <section class="text-center lg:text-left max-w-2xl space-y-8">
                <!-- Icon Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-2 glass-card rounded-full">
                    <i class="fas fa-star text-yellow-400"></i>
                    <span class="text-gray-300 text-sm font-medium">Experiencia Premium</span>
                </div>

                <!-- Title -->
                <h2 class="text-5xl md:text-7xl font-extrabold drop-shadow-2xl">
                    <span class="brand-gradient">Bienvenido a</span>
                    <br>
                    <span class="text-white">Restaurante Piscina</span>
                </h2>

                <!-- Description -->
                <p class="text-xl md:text-2xl text-gray-300 leading-relaxed">
                    Vive una experiencia única en nuestro restaurante y piscina en <span class="text-cyan-400 font-semibold">Morroa, Sucre</span>.
                    Disfruta de un ambiente refrescante, excelente gastronomía y atención de primera.
                </p>

                <!-- Features Grid -->
                <div class="grid grid-cols-2 gap-4 py-4">
                    <div class="glass-card p-4 rounded-xl">
                        <i class="fas fa-swimming-pool text-3xl text-cyan-400 mb-2"></i>
                        <h3 class="text-white font-semibold">Piscina</h3>
                        <p class="text-gray-400 text-sm">Refrescante</p>
                    </div>
                    <div class="glass-card p-4 rounded-xl">
                        <i class="fas fa-utensils text-3xl text-green-400 mb-2"></i>
                        <h3 class="text-white font-semibold">Gastronomía</h3>
                        <p class="text-gray-400 text-sm">Deliciosa</p>
                    </div>
                    <div class="glass-card p-4 rounded-xl">
                        <i class="fas fa-users text-3xl text-yellow-400 mb-2"></i>
                        <h3 class="text-white font-semibold">Familia</h3>
                        <p class="text-gray-400 text-sm">Para todos</p>
                    </div>
                    <div class="glass-card p-4 rounded-xl">
                        <i class="fas fa-calendar-alt text-3xl text-orange-400 mb-2"></i>
                        <h3 class="text-white font-semibold">Eventos</h3>
                        <p class="text-gray-400 text-sm">Especiales</p>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap justify-center lg:justify-start gap-4 pt-4">
                    <a href="{{ route('menu.digital') }}"
                       class="px-8 py-4 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-bold rounded-lg shadow-2xl shadow-cyan-500/50 hover:shadow-cyan-500/70 transition-all transform hover:scale-105 flex items-center gap-2">
                        <i class="fas fa-mobile-alt text-xl"></i>
                        Ver Carta Digital
                    </a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="px-8 py-4 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-bold rounded-lg shadow-2xl shadow-green-500/50 hover:shadow-green-500/70 transition-all transform hover:scale-105 flex items-center gap-2">
                                <i class="fas fa-tachometer-alt text-xl"></i>
                                Ir al Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="px-8 py-4 bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-white font-bold rounded-lg shadow-2xl shadow-yellow-500/50 hover:shadow-yellow-500/70 transition-all transform hover:scale-105 flex items-center gap-2">
                                <i class="fas fa-rocket text-xl"></i>
                                Comenzar Ahora
                            </a>
                        @endauth
                    @endif
                </div>

                <!-- Tags -->
                <div class="flex flex-wrap gap-2 pt-6 justify-center lg:justify-start">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                        <i class="fas fa-water"></i> Piscina
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-300 border border-green-500/30">
                        <i class="fas fa-leaf"></i> Gastronomía
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">
                        <i class="fas fa-heart"></i> Familia
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-orange-500/20 text-orange-300 border border-orange-500/30">
                        <i class="fas fa-glass-cheers"></i> Eventos
                    </span>
                </div>
            </section>

            <!-- Image -->
            <section class="hidden lg:block floating">
                <div class="relative">
                    <div class="absolute -inset-4 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-3xl blur-2xl opacity-30"></div>
                    <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=600&q=80"
                         alt="Restaurante Piscina"
                         class="relative rounded-2xl shadow-2xl border-2 border-cyan-500/50 hover:scale-105 transition duration-500 w-[500px] h-[600px] object-cover">
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 glass-card mt-20 py-8">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-gray-400 flex items-center justify-center gap-2">
                <i class="fas fa-swimming-pool text-cyan-400"></i>
                <span>Restaurante Piscina &copy; {{ date('Y') }} - Morroa, Sucre</span>
            </p>
        </div>
    </footer>
</body>
</html>
