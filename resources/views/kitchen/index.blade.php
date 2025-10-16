<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Panel de Cocina') }}
            </h2>

            <!-- Indicador de estado del sistema -->
            <div class="flex items-center space-x-4">
                <div class="connection-status show" id="kitchen-connection-status">
                    <div class="connection-indicator connected" id="kitchen-connection-indicator"></div>
                    <span id="kitchen-connection-text">Sistema Conectado</span>
                </div>

                <div class="text-sm text-gray-500">
                    Última actualización: <span id="last-update">{{ now()->format('H:i:s') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Componente principal de cocina con notificaciones -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <livewire:kitchen-notifications />
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Script específico para cocina
            document.addEventListener('DOMContentLoaded', function() {
                console.log('🍳 Panel de cocina iniciado');

                // Configuración específica para cocina
                window.kitchenMode = true;

                // Auto-refresh cada 30 segundos
                setInterval(function() {
                    Livewire.dispatch('kitchen.refresh');
                    document.getElementById('last-update').textContent = new Date().toLocaleTimeString();
                }, 30000);

                // Sonidos específicos de cocina
                window.playCookingSound = function() {
                    if (window.notificationManager) {
                        window.notificationManager.playNotificationSound('kitchen');
                    }
                };
            });

            // Listener para notificaciones de nuevos pedidos
            document.addEventListener('livewire:init', () => {
                Livewire.on('order.new', (data) => {
                    console.log('🆕 Nuevo pedido recibido:', data);
                    window.playCookingSound();
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            /* Estilos específicos para cocina */
            .kitchen-alert {
                animation: kitchenPulse 1s ease-in-out infinite;
            }

            .urgent-order {
                border-left: 4px solid #ef4444;
                background: linear-gradient(90deg, #fee2e2 0%, #ffffff 10%);
            }

            .ready-order {
                border-left: 4px solid #10b981;
                background: linear-gradient(90deg, #d1fae5 0%, #ffffff 10%);
            }

            @keyframes kitchenPulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.02); }
            }

            /* Indicadores de tiempo para órdenes */
            .order-timer {
                display: inline-block;
                padding: 2px 8px;
                border-radius: 12px;
                font-size: 11px;
                font-weight: 600;
            }

            .timer-green { background: #d1fae5; color: #065f46; }
            .timer-yellow { background: #fef3c7; color: #92400e; }
            .timer-red { background: #fee2e2; color: #991b1b; }
        </style>
    @endpush
</x-app-layout>