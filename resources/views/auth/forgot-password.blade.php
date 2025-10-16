<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-key text-6xl text-cyan-400 mb-2"></i>
                    <h1 class="text-2xl font-bold text-white">Recuperar Contraseña</h1>
                </div>
            </div>
        </x-slot>

        <div class="mb-6 p-4 bg-cyan-500/10 border border-cyan-500/20 rounded-lg">
            <p class="text-sm text-gray-300 flex items-start gap-2">
                <i class="fas fa-info-circle text-cyan-400 mt-0.5"></i>
                <span>¿Olvidaste tu contraseña? No hay problema. Ingresa tu correo electrónico y te enviaremos un enlace para restablecerla.</span>
            </p>
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm bg-gradient-to-r from-green-500/20 to-emerald-500/20 border border-green-400/30 text-green-100 rounded-lg p-3">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2 text-green-400"></i>
                    {{ $value }}
                </div>
            </div>
        @endsession>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <div>
                <x-label for="email" value="Correo electrónico" class="text-gray-300 font-medium mb-2" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-cyan-400"></i>
                    </div>
                    <x-input id="email"
                             class="block mt-1 w-full pl-10 bg-white/5 border-cyan-500/30 text-white placeholder-gray-400 rounded-lg focus:border-cyan-400 focus:ring focus:ring-cyan-400/50 backdrop-blur-sm transition-all"
                             type="email"
                             name="email"
                             :value="old('email')"
                             required
                             autofocus
                             autocomplete="username"
                             placeholder="tu.email@restaurante.com" />
                </div>
            </div>

            <div class="flex flex-col space-y-3">
                <x-button class="w-full justify-center bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-semibold py-3 px-4 rounded-lg shadow-lg shadow-cyan-500/50 hover:shadow-cyan-500/70 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Enviar Enlace de Recuperación
                </x-button>

                <a href="{{ route('login') }}" class="text-center text-sm text-cyan-400 hover:text-cyan-300 underline transition-colors flex items-center justify-center gap-1">
                    <i class="fas fa-arrow-left text-xs"></i>
                    Volver al inicio de sesión
                </a>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
