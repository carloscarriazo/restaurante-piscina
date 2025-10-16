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

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Name Input -->
            <div>
                <x-label for="name" value="Nombre" class="text-gray-300 font-medium mb-2" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-cyan-400"></i>
                    </div>
                    <x-input id="name"
                             class="block mt-1 w-full pl-10 bg-white/5 border-cyan-500/30 text-white placeholder-gray-400 rounded-lg focus:border-cyan-400 focus:ring focus:ring-cyan-400/50 backdrop-blur-sm transition-all"
                             type="text"
                             name="name"
                             :value="old('name')"
                             required
                             autofocus
                             autocomplete="name"
                             placeholder="Tu nombre completo" />
                </div>
            </div>

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
                             autocomplete="new-password"
                             placeholder="Mínimo 8 caracteres" />
                </div>
            </div>

            <!-- Password Confirmation Input -->
            <div>
                <x-label for="password_confirmation" value="Confirmar Contraseña" class="text-gray-300 font-medium mb-2" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-cyan-400"></i>
                    </div>
                    <x-input id="password_confirmation"
                             class="block mt-1 w-full pl-10 bg-white/5 border-cyan-500/30 text-white placeholder-gray-400 rounded-lg focus:border-cyan-400 focus:ring focus:ring-cyan-400/50 backdrop-blur-sm transition-all"
                             type="password"
                             name="password_confirmation"
                             required
                             autocomplete="new-password"
                             placeholder="Repite tu contraseña" />
                </div>
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required class="rounded border-cyan-500/30 text-cyan-500 shadow-sm focus:ring-cyan-500 bg-white/5" />

                            <div class="ms-2 text-sm text-gray-300">
                                {!! __('Acepto los :terms_of_service y :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-cyan-400 hover:text-cyan-300">'.__('Términos de Servicio').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-cyan-400 hover:text-cyan-300">'.__('Política de Privacidad').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-between mt-6 pt-4 border-t border-cyan-500/20">
                <a class="text-sm text-cyan-400 hover:text-cyan-300 underline rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-colors flex items-center gap-1"
                   href="{{ route('login') }}">
                    <i class="fas fa-arrow-left text-xs"></i>
                    ¿Ya tienes cuenta?
                </a>

                <x-button class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-semibold py-2 px-6 rounded-lg shadow-lg shadow-cyan-500/50 hover:shadow-cyan-500/70 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-user-plus mr-2"></i>
                    Registrarse
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
