<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Meseros - Restaurante</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .notification-item {
            transition: all 0.3s ease;
        }
        .notification-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .order-status-ready {
            background: linear-gradient(45deg, #10b981, #059669);
            color: white;
        }
        .order-status-delivered {
            background: linear-gradient(45deg, #6b7280, #4b5563);
            color: white;
        }
    </style>
</head>
<body>
    <div class="min-h-screen p-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="card rounded-xl shadow-xl p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Dashboard Meseros</h1>
                        <p class="text-gray-600 mt-2">Gestión de pedidos y notificaciones</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Hora actual</p>
                            <p class="text-lg font-semibold text-gray-800" id="current-time"></p>
                        </div>
                        <button onclick="refreshData()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Listos para Servir</p>
                        <p class="text-2xl font-semibold text-gray-900" id="ready-orders-count">0</p>
                    </div>
                </div>
            </div>

            <div class="card rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5l-5-5h5v-5a7.5 7.5 0 110-15"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">En Proceso</p>
                        <p class="text-2xl font-semibold text-gray-900" id="in-process-count">0</p>
                    </div>
                </div>
            </div>

            <div class="card rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Notificaciones</p>
                        <p class="text-2xl font-semibold text-gray-900" id="notifications-count">0</p>
                    </div>
                </div>
            </div>

            <div class="card rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Entregados Hoy</p>
                        <p class="text-2xl font-semibold text-gray-900" id="delivered-today">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Pedidos Listos para Servir -->
            <div class="lg:col-span-2">
                <div class="card rounded-xl shadow-xl p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">🍽️ Pedidos Listos para Servir</h2>
                        <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full" id="ready-orders-badge">0 pedidos</span>
                    </div>
                    <div id="ready-orders-container" class="space-y-4">
                        <!-- Los pedidos listos se cargarán aquí -->
                    </div>
                </div>
            </div>

            <!-- Notificaciones -->
            <div>
                <div class="card rounded-xl shadow-xl p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">🔔 Notificaciones</h2>
                        <span class="bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full" id="notifications-badge">0 nuevas</span>
                    </div>
                    <div id="notifications-container" class="space-y-3 max-h-96 overflow-y-auto">
                        <!-- Las notificaciones se cargarán aquí -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Todos los Pedidos -->
        <div class="mt-8">
            <div class="card rounded-xl shadow-xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">📋 Todos los Pedidos del Día</h2>
                    <div class="flex space-x-2">
                        <button onclick="filterOrders('all')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors" id="filter-all">Todos</button>
                        <button onclick="filterOrders('ready')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors" id="filter-ready">Listos</button>
                        <button onclick="filterOrders('delivered')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors" id="filter-delivered">Entregados</button>
                    </div>
                </div>
                <div id="all-orders-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Todos los pedidos se cargarán aquí -->
                </div>
            </div>
        </div>
    </div>

    <script>
        let orders = [];
        let notifications = [];
        let currentFilter = 'all';

        // Actualizar hora actual
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleTimeString();
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Cargar datos
        async function loadReadyOrders() {
            try {
                const response = await fetch('/api/waiter/orders/ready');
                const data = await response.json();

                if (data.success) {
                    displayReadyOrders(data.data);
                    document.getElementById('ready-orders-count').textContent = data.data.length;
                    document.getElementById('ready-orders-badge').textContent = `${data.data.length} pedidos`;
                }
            } catch (error) {
                console.error('Error cargando pedidos listos:', error);
            }
        }

        async function loadAllOrders() {
            try {
                const response = await fetch('/api/waiter/orders');
                const data = await response.json();

                if (data.success) {
                    orders = data.data;
                    displayAllOrders();
                    updateStats();
                }
            } catch (error) {
                console.error('Error cargando todos los pedidos:', error);
            }
        }

        async function loadNotifications() {
            try {
                const response = await fetch('/api/waiter/notifications');
                const data = await response.json();

                if (data.success) {
                    notifications = data.data;
                    displayNotifications();
                    document.getElementById('notifications-count').textContent = data.meta.unread_count;
                    document.getElementById('notifications-badge').textContent = `${data.meta.unread_count} nuevas`;
                }
            } catch (error) {
                console.error('Error cargando notificaciones:', error);
            }
        }

        // Mostrar pedidos listos
        function displayReadyOrders(readyOrders) {
            const container = document.getElementById('ready-orders-container');

            if (readyOrders.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-lg font-medium">No hay pedidos listos</p>
                        <p class="text-sm">Los pedidos aparecerán aquí cuando estén listos para servir</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = readyOrders.map(order => `
                <div class="notification-item bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-lg p-4 pulse-animation">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full mr-3">LISTO</span>
                                <h3 class="font-semibold text-lg text-gray-800">Pedido #${order.id}</h3>
                            </div>
                            <div class="space-y-1 text-sm text-gray-600">
                                <p><span class="font-medium">Mesa:</span> ${order.table_name}</p>
                                <p><span class="font-medium">Hora:</span> ${order.completed_at}</p>
                                <p><span class="font-medium">Tiempo preparación:</span> ${Math.round(order.preparation_time || 0)} min</p>
                            </div>
                        </div>
                        <button onclick="markAsDelivered(${order.id})" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            ✅ Marcar Entregado
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Mostrar notificaciones
        function displayNotifications() {
            const container = document.getElementById('notifications-container');

            if (notifications.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5l-5-5h5v-4a7.5 7.5 0 110-15"></path>
                        </svg>
                        <p class="text-sm">No hay notificaciones</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = notifications.map(notification => `
                <div class="notification-item bg-yellow-50 border border-yellow-200 rounded-lg p-3 ${!notification.is_read ? 'border-l-4 border-l-yellow-500' : ''}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800 text-sm">${notification.title}</p>
                            <p class="text-gray-600 text-xs mt-1">${notification.message}</p>
                            <p class="text-gray-400 text-xs mt-1">${notification.time_ago}</p>
                        </div>
                        ${!notification.is_read ? `
                            <button onclick="markNotificationAsRead(${notification.id})" class="text-yellow-600 hover:text-yellow-800 text-xs">
                                ✓ Marcar leído
                            </button>
                        ` : ''}
                    </div>
                </div>
            `).join('');
        }

        // Mostrar todos los pedidos
        function displayAllOrders() {
            const container = document.getElementById('all-orders-container');
            let filteredOrders = orders;

            if (currentFilter === 'ready') {
                filteredOrders = orders.filter(order => order.status_enum === 'ready');
            } else if (currentFilter === 'delivered') {
                filteredOrders = orders.filter(order => order.status_enum === 'delivered');
            }

            container.innerHTML = filteredOrders.map(order => `
                <div class="bg-white border rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-semibold text-gray-800">Pedido #${order.id}</h3>
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusClass(order.status_enum)}">
                            ${order.status}
                        </span>
                    </div>
                    <div class="space-y-1 text-sm text-gray-600">
                        <p><span class="font-medium">Mesa:</span> ${order.table_name}</p>
                        <p><span class="font-medium">Hora:</span> ${order.created_at}</p>
                        <p><span class="font-medium">Total:</span> $${order.total}</p>
                    </div>
                    ${order.status_enum === 'ready' ? `
                        <button onclick="markAsDelivered(${order.id})" class="mt-3 w-full bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm font-medium transition-colors">
                            Marcar como Entregado
                        </button>
                    ` : ''}
                </div>
            `).join('');
        }

        // Actualizar estadísticas
        function updateStats() {
            const readyCount = orders.filter(order => order.status_enum === 'ready').length;
            const inProcessCount = orders.filter(order => order.status_enum === 'in_process').length;
            const deliveredToday = orders.filter(order => order.status_enum === 'delivered').length;

            document.getElementById('ready-orders-count').textContent = readyCount;
            document.getElementById('in-process-count').textContent = inProcessCount;
            document.getElementById('delivered-today').textContent = deliveredToday;
        }

        // Obtener clase CSS para el estado
        function getStatusClass(status) {
            switch(status) {
                case 'ready': return 'bg-green-100 text-green-800';
                case 'delivered': return 'bg-gray-100 text-gray-800';
                case 'in_process': return 'bg-blue-100 text-blue-800';
                case 'pending': return 'bg-yellow-100 text-yellow-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        // Filtrar pedidos
        function filterOrders(filter) {
            currentFilter = filter;

            // Actualizar botones
            document.querySelectorAll('[id^="filter-"]').forEach(btn => {
                btn.classList.remove('bg-blue-500', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });

            document.getElementById(`filter-${filter}`).classList.remove('bg-gray-200', 'text-gray-700');
            document.getElementById(`filter-${filter}`).classList.add('bg-blue-500', 'text-white');

            displayAllOrders();
        }

        // Marcar pedido como entregado
        async function markAsDelivered(orderId) {
            try {
                const response = await fetch(`/api/waiter/orders/${orderId}/mark-delivered`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Refrescar datos
                    await refreshData();

                    // Mostrar mensaje de éxito
                    showNotification('Pedido marcado como entregado exitosamente', 'success');
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error marcando pedido como entregado:', error);
                showNotification('Error de conexión', 'error');
            }
        }

        // Marcar notificación como leída
        async function markNotificationAsRead(notificationId) {
            try {
                const response = await fetch(`/api/waiter/notifications/${notificationId}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    await loadNotifications();
                }
            } catch (error) {
                console.error('Error marcando notificación como leída:', error);
            }
        }

        // Mostrar notificación temporal
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Refrescar todos los datos
        async function refreshData() {
            await Promise.all([
                loadReadyOrders(),
                loadAllOrders(),
                loadNotifications()
            ]);
        }

        // Cargar datos iniciales
        document.addEventListener('DOMContentLoaded', function() {
            refreshData();

            // Refrescar automáticamente cada 30 segundos
            setInterval(refreshData, 30000);
        });
    </script>
</body>
</html>
