<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
    <!-- Ocean Pattern Background -->
    <div class="absolute inset-0 opacity-5">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="ocean-waves" x="0" y="0" width="100" height="20" patternUnits="userSpaceOnUse">
                    <path d="M0,10 Q25,0 50,10 T100,10" stroke="#06b6d4" stroke-width="2" fill="none"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#ocean-waves)"/>
        </svg>
    </div>

    <!-- Logo Container with Floating Animation -->
    <div class="floating mb-8 relative z-10">
        {{ $logo }}
    </div>

    <!-- Main Card with Ocean Glassmorphism -->
    <div class="w-full sm:max-w-md mt-6 px-8 py-8 glass-effect shadow-2xl overflow-hidden sm:rounded-2xl relative shimmer">
        <!-- Ocean Glow Effect -->
        <div class="absolute -inset-1 bg-gradient-to-r from-cyan-500/20 via-sky-500/20 to-blue-500/20 rounded-2xl blur-xl opacity-50"></div>

        <!-- Card Content Container -->
        <div class="relative">
            <!-- Card Header -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-full mb-4 shadow-lg shadow-cyan-500/50">
                    <i class="fas fa-utensils text-2xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2 bg-gradient-to-r from-cyan-400 via-sky-400 to-blue-400 bg-clip-text text-transparent">
                    Bienvenido
                </h2>
                <p class="text-gray-300 text-sm">Sistema de gestión del restaurante</p>
            </div>

            <!-- Card Content -->
            <div class="space-y-6">
                {{ $slot }}
            </div>

            <!-- Card Footer -->
            <div class="mt-8 pt-6 border-t border-cyan-500/20">
                <p class="text-center text-xs text-gray-400 flex items-center justify-center gap-2">
                    <i class="fas fa-swimming-pool text-cyan-400"></i>
                    <span>Restaurante Piscina &copy; {{ date('Y') }}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute bottom-10 left-10 text-cyan-500/30 text-xs hidden sm:flex items-center gap-2">
        <i class="fas fa-code"></i>
        <span>Sistema Ocean v2.0</span>
    </div>

    <div class="absolute top-10 right-10 text-cyan-500/30 text-xs hidden sm:flex items-center gap-2">
        <i class="fas fa-shield-alt"></i>
        <span>Acceso Seguro</span>
    </div>
</div>
