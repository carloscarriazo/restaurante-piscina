<x-app-layout>
    <x-slot name="title">Mi Perfil</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="ocean-card p-6 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-full flex items-center justify-center shadow-lg shadow-cyan-500/50">
                    <i class="fas fa-user text-3xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white mb-1">
                        Mi Perfil
                    </h1>
                    <p class="text-gray-300">Administra tu información personal y configuración de cuenta</p>
                </div>
            </div>
        </div>

        <!-- Profile Sections -->
        <div class="space-y-6">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <div class="ocean-card p-6">
                    @livewire('profile.update-profile-information-form')
                </div>
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="ocean-card p-6">
                    @livewire('profile.update-password-form')
                </div>
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="ocean-card p-6">
                    @livewire('profile.two-factor-authentication-form')
                </div>
            @endif

            <div class="ocean-card p-6">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <div class="ocean-card p-6 border-red-500/30">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
