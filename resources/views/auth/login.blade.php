<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-swimming-pool text-6xl text-cyan-400 mb-2"></i>
                    <h1 class="text-2xl font-bold text-white">Restaurante Piscina</h1>
                </div>
            </div>
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm bg-gradient-to-r from-cyan-500/20 to-blue-500/20 border border-cyan-400/30 rounded-lg p-3 text-cyan-100">
                <div class="flex items-center">
                    <i class="fas fa-info-circle mr-2 text-cyan-400"></i>
                    {{ $value }}
                </div>
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email Input -->
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

            <!-- Password Input -->
            <div>
                <x-label for="password" value="Contraseña" class="text-gray-300 font-medium mb-2" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-cyan-400"></i>
                    </div>
                    <x-input id="password"
                             class="block mt-1 w-full pl-10 bg-white/5 border-cyan-500/30 text-white placeholder-gray-400 rounded-lg focus:border-cyan-400 focus:ring focus:ring-cyan-400/50 backdrop-blur-sm transition-all"
                             type="password"
                             name="password"
                             required
                             autocomplete="current-password"
                             placeholder="••••••••" />
                </div>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center group cursor-pointer">
                    <x-checkbox id="remember_me"
                                name="remember"
                                class="rounded border-cyan-500/30 text-cyan-500 shadow-sm focus:ring-cyan-500 bg-white/5" />
                    <span class="ms-2 text-sm text-gray-300 group-hover:text-cyan-400 transition-colors">
                        Recordarme
                    </span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-cyan-400 hover:text-cyan-300 underline rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-colors duration-200 flex items-center gap-1"
                       href="{{ route('password.request') }}">
                        <i class="fas fa-key text-xs"></i>
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <!-- Login Button -->
            <div class="flex flex-col space-y-3">
                <x-button class="w-full justify-center bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-semibold py-3 px-4 rounded-lg shadow-lg shadow-cyan-500/50 hover:shadow-cyan-500/70 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Iniciar Sesión
                </x-button>

                <div class="text-center">
                    <p class="text-xs text-gray-400 flex items-center justify-center gap-1">
                        <i class="fas fa-info-circle text-cyan-400"></i>
                        Para acceder como mesero, utiliza las credenciales proporcionadas
                    </p>
                </div>
            </div>
        </form>

        <!-- Quick Access Info -->
        <div class="mt-6 p-4 bg-gradient-to-br from-cyan-500/10 to-blue-500/10 rounded-lg border border-cyan-500/20">
            <h4 class="text-sm font-semibold text-cyan-400 mb-3 flex items-center">
                <i class="fas fa-user-shield mr-2"></i>
                Acceso Rápido para Pruebas
            </h4>
            <div class="space-y-2 text-xs text-gray-300">
                <div class="flex items-center justify-between p-2 bg-white/5 rounded">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-crown text-yellow-400"></i>
                        <strong>Administrador:</strong>
                    </span>
                    <span class="text-cyan-400">admin@bluelagoon.com</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-white/5 rounded">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-user text-green-400"></i>
                        <strong>Mesero:</strong>
                    </span>
                    <span class="text-cyan-400">mesero1@bluelagoon.com</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-white/5 rounded">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-key text-blue-400"></i>
                        <strong>Contraseña:</strong>
                    </span>
                    <span class="text-cyan-400 font-mono">password</span>
                </div>
            </div>
        </div>
    </x-authentication-card>
</x-guest-layout>
