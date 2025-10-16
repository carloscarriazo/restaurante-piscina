<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Panel de Meseros') }}
            </h2>

            <!-- Indicadores de estado -->
            <div class="flex items-center space-x-4">
                <!-- Estadísticas rápidas de notificaciones -->
                @if(isset($userNotificationStats))
                    <div class="flex items-center space-x-2 text-sm">
                        @if($userNotificationStats['unread_total'] > 0)
                            <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                {{ $userNotificationStats['unread_total'] }} sin leer
                            </span>
                        @endif

                        @if($userNotificationStats['critical_unread'] > 0)
                            <span class="inline-flex items-center px-2 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full">
                                {{ $userNotificationStats['critical_unread'] }} urgentes
                            </span>
                        @endif

                        <span class="text-gray-500">
                            Hoy: {{ $userNotificationStats['today_total'] ?? 0 }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Grid de componentes para meseros -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Panel principal de pedidos -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Mis Pedidos Activos</h3>
                        <livewire:order-manager />
                    </div>
                </div>

                <!-- Panel lateral de notificaciones y mesas -->
                <div class="space-y-6">
                    <!-- Panel de notificaciones -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Notificaciones</h3>
                            <div class="space-y-4">
                                <livewire:real-time-notifications />
                            </div>
                        </div>
                    </div>

                    <!-- Estado de mesas -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Estado de Mesas</h3>
                            <livewire:table-status />
                        </div>
                    </div>

                    <!-- Pedidos listos para servir -->
                    <div class="bg-green-50 border border-green-200 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-green-800 mb-4">
                                🍽️ Listos para Servir
                            </h3>
                            <div id="ready-orders" class="space-y-2">
                                <!-- Se llenará dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('👨‍🍳 Panel de meseros iniciado');

                // Configuración específica para meseros
                window.waiterMode = true;

                // Cargar pedidos listos al iniciar
                loadReadyOrders();

                // Auto-refresh cada 15 segundos para meseros (más frecuente)
                setInterval(function() {
                    loadReadyOrders();
                    Livewire.dispatch('notification.refresh');
                }, 15000);
            });

            // Función para cargar pedidos listos
            async function loadReadyOrders() {
                try {
                    const response = await fetch('/api/waiter/orders/ready', {
                        headers: {
                            'Authorization': `Bearer ${getAuthToken()}`,
                            'Content-Type': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        updateReadyOrdersUI(data.data || []);
                    }
                } catch (error) {
                    console.error('Error cargando pedidos listos:', error);
                }
            }

            // Actualizar UI de pedidos listos
            function updateReadyOrdersUI(orders) {
                const container = document.getElementById('ready-orders');

                if (orders.length === 0) {
                    container.innerHTML = '<p class="text-green-600 text-sm">No hay pedidos listos</p>';
                    return;
                }

                container.innerHTML = orders.map(order => `
                    <div class="bg-white border border-green-200 rounded-lg p-3 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-medium text-green-800">${order.code || '#' + order.id}</span>
                                <span class="text-sm text-gray-600 ml-2">${order.table_name}</span>
                            </div>
                            <button onclick="markAsServed(${order.id})"
                                    class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">
                                Servido
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">${order.items_count} productos</p>
                    </div>
                `).join('');
            }

            // Marcar como servido
            async function markAsServed(orderId) {
                try {
                    const response = await fetch(`/api/waiter/orders/${orderId}/mark-delivered`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${getAuthToken()}`,
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        }
                    });

                    if (response.ok) {
                        loadReadyOrders();
                        showToast('success', 'Pedido Servido', 'El pedido ha sido marcado como servido');

                        // Actualizar componente Livewire
                        Livewire.dispatch('order.served', { orderId });
                    } else {
                        throw new Error('Error en la respuesta del servidor');
                    }
                } catch (error) {
                    console.error('Error marcando como servido:', error);
                    showToast('error', 'Error', 'No se pudo marcar el pedido como servido');
                }
            }

            // Función auxiliar para obtener token
            function getAuthToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            }

            // Función auxiliar para mostrar toast
            function showToast(type, title, message) {
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type, title, message }
                }));
            }

            // Listeners específicos para meseros
            document.addEventListener('livewire:init', () => {
                // Escuchar cuando un pedido esté listo
                Livewire.on('order.ready', (data) => {
                    console.log('🍽️ Pedido listo:', data);
                    loadReadyOrders();

                    // Reproducir sonido específico
                    if (window.notificationManager) {
                        window.notificationManager.playNotificationSound('kitchen');
                    }
                });

                // Escuchar actualizaciones de pedidos
                Livewire.on('order.updated', (data) => {
                    console.log('📝 Pedido actualizado:', data);
                    loadReadyOrders();
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            /* Estilos específicos para meseros */
            .notification-pulse {
                animation: pulse 2s infinite;
            }

            .ready-order-item {
                border-left: 4px solid #10b981;
                background: linear-gradient(90deg, #ecfdf5 0%, #ffffff 20%);
            }

            .urgent-notification {
                border-left: 4px solid #ef4444;
                background: linear-gradient(90deg, #fee2e2 0%, #ffffff 20%);
                animation: urgentPulse 1.5s ease-in-out infinite;
            }

            @keyframes urgentPulse {
                0%, 100% {
                    transform: scale(1);
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }
                50% {
                    transform: scale(1.02);
                    box-shadow: 0 4px 8px rgba(239, 68, 68, 0.2);
                }
            }

            /* Indicadores de estado de mesa */
            .table-free { background: #d1fae5; border-color: #10b981; }
            .table-occupied { background: #fee2e2; border-color: #ef4444; }
            .table-reserved { background: #fef3c7; border-color: #f59e0b; }
        </style>
    @endpush
</x-app-layout>