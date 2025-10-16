<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Restaurante Piscina - Sistema de Gestión</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Ocean Theme Styles -->
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }

            /* Ocean Gradient Background */
            .bg-ocean-gradient {
                background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
                position: relative;
                overflow: hidden;
            }

            .bg-ocean-gradient::before {
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

            /* Glass Effect */
            .glass-effect {
                background: rgba(15, 23, 42, 0.8);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(6, 182, 212, 0.2);
                box-shadow:
                    0 0 40px rgba(6, 182, 212, 0.1),
                    inset 0 0 20px rgba(6, 182, 212, 0.05);
            }

            /* Floating Animation */
            .floating {
                animation: float 6s ease-in-out infinite;
            }

            @keyframes float {
                0%, 100% {
                    transform: translateY(0px) rotate(0deg);
                }
                50% {
                    transform: translateY(-20px) rotate(2deg);
                }
            }

            /* Shimmer Effect */
            .shimmer {
                position: relative;
                overflow: hidden;
            }

            .shimmer::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(
                    90deg,
                    transparent,
                    rgba(6, 182, 212, 0.2),
                    transparent
                );
                animation: shimmer 3s infinite;
            }

            @keyframes shimmer {
                0% { left: -100%; }
                100% { left: 100%; }
            }

            /* Particle Effect */
            .particle {
                position: absolute;
                border-radius: 50%;
                pointer-events: none;
                animation: particleFloat 20s infinite;
                opacity: 0.3;
            }

            @keyframes particleFloat {
                0% {
                    transform: translateY(100vh) translateX(0) rotate(0deg);
                    opacity: 0;
                }
                10% {
                    opacity: 0.3;
                }
                90% {
                    opacity: 0.3;
                }
                100% {
                    transform: translateY(-100vh) translateX(100px) rotate(360deg);
                    opacity: 0;
                }
            }

            /* Ocean Wave Animation */
            .ocean-wave {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 200px;
                background: linear-gradient(180deg, transparent, rgba(6, 182, 212, 0.1));
                opacity: 0.3;
            }
        </style>

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="bg-ocean-gradient min-h-screen">
        <!-- Particles -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="particle w-2 h-2 bg-cyan-400" style="left: 10%; animation-delay: 0s;"></div>
            <div class="particle w-3 h-3 bg-sky-400" style="left: 30%; animation-delay: 3s;"></div>
            <div class="particle w-1 h-1 bg-blue-400" style="left: 50%; animation-delay: 6s;"></div>
            <div class="particle w-2 h-2 bg-cyan-300" style="left: 70%; animation-delay: 9s;"></div>
            <div class="particle w-3 h-3 bg-sky-300" style="left: 90%; animation-delay: 12s;"></div>
            <div class="particle w-2 h-2 bg-blue-300" style="left: 20%; animation-delay: 15s;"></div>
        </div>

        <!-- Ocean Wave -->
        <div class="ocean-wave"></div>

        <div class="font-sans text-white antialiased relative z-10">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
