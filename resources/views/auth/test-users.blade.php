@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-2xl w-full">
        <div class="glass-card rounded-2xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2 font-['Playfair_Display']">
                    Blue Lagoon Restaurant
                </h1>
                <p class="text-blue-100 opacity-90">Sistema de Gestión - Usuarios de Prueba</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Administrador -->
                <div class="bg-white/10 rounded-xl p-6 border border-white/20">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-white font-semibold">Administrador</h3>
                            <p class="text-blue-100 text-sm opacity-75">Acceso completo al sistema</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <p class="text-blue-100"><strong>Email:</strong> admin@bluelagoon.com</p>
                        <p class="text-blue-100"><strong>Contraseña:</strong> password</p>
                        <p class="text-blue-100 text-xs opacity-75">Gestión completa, reportes, configuración</p>
                    </div>
                </div>

                <!-- Mesero 1 -->
                <div class="bg-white/10 rounded-xl p-6 border border-white/20">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-600 to-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-white font-semibold">Mesero Principal</h3>
                            <p class="text-blue-100 text-sm opacity-75">Carlos Rodríguez</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <p class="text-blue-100"><strong>Email:</strong> mesero1@bluelagoon.com</p>
                        <p class="text-blue-100"><strong>Contraseña:</strong> password</p>
                        <p class="text-blue-100 text-xs opacity-75">Gestión de pedidos, mesas, productos</p>
                    </div>
                </div>

                <!-- Mesero 2 -->
                <div class="bg-white/10 rounded-xl p-6 border border-white/20">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-600 to-purple-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-white font-semibold">Mesero Terraza</h3>
                            <p class="text-blue-100 text-sm opacity-75">Ana García</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <p class="text-blue-100"><strong>Email:</strong> mesero2@bluelagoon.com</p>
                        <p class="text-blue-100"><strong>Contraseña:</strong> password</p>
                        <p class="text-blue-100 text-xs opacity-75">Especialista en área de piscina</p>
                    </div>
                </div>

                <!-- Chef -->
                <div class="bg-white/10 rounded-xl p-6 border border-white/20">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-600 to-orange-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-white font-semibold">Chef Principal</h3>
                            <p class="text-blue-100 text-sm opacity-75">Miguel Torres</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <p class="text-blue-100"><strong>Email:</strong> chef@bluelagoon.com</p>
                        <p class="text-blue-100"><strong>Contraseña:</strong> password</p>
                        <p class="text-blue-100 text-xs opacity-75">Gestión de cocina y menús</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Ir al Login
                </a>
            </div>

            <div class="mt-6 p-4 bg-white/5 rounded-lg border border-white/10">
                <h4 class="text-sm font-semibold text-white mb-2">Nota de Desarrollo</h4>
                <p class="text-xs text-blue-100 opacity-80">
                    Estos usuarios fueron creados automáticamente por el UserSeeder.
                    En producción, configura credenciales seguras a través del panel de administración.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
