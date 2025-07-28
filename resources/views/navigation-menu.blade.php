<nav x-data="{ open: false }" class="bg-blue-100 border-b border-blue-300 shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-4">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <x-application-mark class="block h-9 w-auto" />
                    <span class="text-blue-800 font-bold text-lg">PiscinaRest</span>
                </a>

                <!-- Navegación Principal -->
                <div class="hidden sm:flex sm:space-x-8 ms-6">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Inicio') }}
                    </x-nav-link>
                    <x-nav-link href="*" :active="request()->routeIs('pedidos.*')">
                        {{ __('Pedidos') }}
                    </x-nav-link>
                    <x-nav-link href="*" :active="request()->routeIs('cocina.*')">
                        {{ __('Cocina') }}
                    </x-nav-link>
                    <x-nav-link href="*" :active="request()->routeIs('facturacion.*')">
                        {{ __('Facturación') }}
                    </x-nav-link>
                    <x-nav-link href="*" :active="request()->routeIs('inventario.*')">
                        {{ __('Inventario') }}
                    </x-nav-link>
                    <x-nav-link href="*" :active="request()->routeIs('configuracion.*')">
                        {{ __('Configuración') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Usuario + Configuración -->
            <div class="hidden sm:flex sm:items-center space-x-4">
                <!-- Foto de Perfil o Nombre -->
                <div class="relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-blue-500 transition">
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-200 hover:text-blue-900 focus:outline-none transition">
                                    {{ Auth::user()->name }}
                                    <svg class="ms-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Configuración -->
                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Perfil') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('Tokens API') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200 my-1"></div>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    {{ __('Cerrar sesión') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Botón Responsive -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-md text-blue-600 hover:bg-blue-200 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open}" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open}" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menú Responsive -->
    <div :class="{'block': open, 'hidden': ! open}" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="*" :active="request()->routeIs('dashboard')">
                {{ __('Inicio') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="*" :active="request()->routeIs('pedidos.*')">
                {{ __('Pedidos') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="*" :active="request()->routeIs('cocina.*')">
                {{ __('Cocina') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="*" :active="request()->routeIs('facturacion.*')">
                {{ __('Facturación') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="*" :active="request()->routeIs('inventario.*')">
                {{ __('Inventario') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="*" :active="request()->routeIs('configuracion.*')">
                {{ __('Configuración') }}
            </x-responsive-nav-link>
        </div>
    </div>
</nav>
