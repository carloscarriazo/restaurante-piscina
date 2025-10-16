<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cocina - Blue Lagoon Restaurant</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        .order-card {
            transition: all 0.3s ease;
        }
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .priority-urgente {
            border-left: 5px solid #ef4444;
            animation: pulse 2s infinite;
        }
        .priority-alta {
            border-left: 5px solid #f97316;
        }
        .priority-media {
            border-left: 5px solid #eab308;
        }
        .priority-normal {
            border-left: 5px solid #22c55e;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .status-badge {
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-preparing {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .auto-refresh {
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-utensils text-2xl"></i>
                    <h1 class="text-2xl font-bold">Vista de Cocina</h1>
                </div>

                <div class="flex items-center space-x-6">
                    <!-- Stats Dashboard -->
                    <div class="flex space-x-4" id="kitchen-stats">
                        <div class="text-center">
                            <div class="text-lg font-bold" id="pending-count">0</div>
                            <div class="text-xs opacity-90">Pendientes</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold" id="preparing-count">0</div>
                            <div class="text-xs opacity-90">Preparando</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold" id="completed-today">0</div>
                            <div class="text-xs opacity-90">Completadas</div>
                        </div>
                    </div>

                    <!-- Auto-refresh Toggle -->
                    <button id="auto-refresh-btn" class="bg-blue-700 hover:bg-blue-800 px-4 py-2 rounded-lg">
                        <i class="fas fa-sync-alt" id="refresh-icon"></i>
                        <span id="refresh-text">Auto</span>
                    </button>

                    <!-- Manual Refresh -->
                    <button id="manual-refresh" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg">
                        <i class="fas fa-refresh"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        <!-- Filter Tabs -->
        <div class="flex space-x-4 mb-6">
            <button class="filter-tab active bg-blue-600 text-white px-6 py-2 rounded-lg" data-filter="all">
                Todas las Órdenes
            </button>
            <button class="filter-tab bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300" data-filter="pending">
                Pendientes
            </button>
            <button class="filter-tab bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300" data-filter="preparing">
                En Preparación
            </button>
        </div>

        <!-- Orders Grid -->
        <div id="orders-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Orders will be loaded here -->
        </div>

        <!-- Loading State -->
        <div id="loading-state" class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
            <p class="text-gray-600">Cargando órdenes...</p>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="text-center py-12 hidden">
            <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No hay órdenes pendientes</h3>
            <p class="text-gray-500">¡Excelente trabajo! Todas las órdenes están completadas.</p>
        </div>
    </main>

    <!-- Order Details Modal -->
    <div id="order-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="sticky top-0 bg-white border-b p-6 flex justify-between items-center">
                    <h2 class="text-2xl font-bold">Detalles de la Orden</h2>
                    <button id="close-modal" class="text-gray-400 hover:text-gray-600 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div id="modal-content" class="p-6">
                    <!-- Modal content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Success Notification -->
    <div id="success-notification" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg hidden z-50">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span id="success-message"></span>
        </div>
    </div>

    <!-- Error Notification -->
    <div id="error-notification" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg hidden z-50">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span id="error-message"></span>
        </div>
    </div>

    <script>
        // Global variables
        let orders = [];
        let autoRefreshInterval;
        let isAutoRefreshEnabled = true;
        let currentFilter = 'all';

        // Initialize app
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
        });

        function initializeApp() {
            loadOrders();
            loadKitchenStats();
            setupEventListeners();
            startAutoRefresh();
        }

        function setupEventListeners() {
            // Filter tabs
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const filter = this.dataset.filter;
                    switchFilter(filter);
                });
            });

            // Auto-refresh toggle
            document.getElementById('auto-refresh-btn').addEventListener('click', toggleAutoRefresh);

            // Manual refresh
            document.getElementById('manual-refresh').addEventListener('click', function() {
                loadOrders();
                loadKitchenStats();
            });

            // Close modal
            document.getElementById('close-modal').addEventListener('click', closeModal);

            // Close modal on background click
            document.getElementById('order-modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        }

        async function loadOrders() {
            try {
                document.getElementById('loading-state').style.display = 'block';
                document.getElementById('orders-container').style.display = 'none';

                const response = await fetch('/api/kitchen/orders');
                const data = await response.json();

                if (data.success) {
                    orders = data.data || [];
                    renderOrders();
                    updateOrderCounts();
                } else {
                    showError('Error al cargar las órdenes');
                }
            } catch (error) {
                showError('Error de conexión: ' + error.message);
            } finally {
                document.getElementById('loading-state').style.display = 'none';
            }
        }

        async function loadKitchenStats() {
            try {
                const response = await fetch('/api/kitchen/stats');
                const data = await response.json();

                if (data.success) {
                    updateStatsDisplay(data.data);
                }
            } catch (error) {
                console.error('Error al cargar estadísticas:', error);
            }
        }

        function renderOrders() {
            const container = document.getElementById('orders-container');

            let filteredOrders = orders;
            if (currentFilter === 'pending') {
                filteredOrders = orders.filter(order => order.status_id === 2);
            } else if (currentFilter === 'preparing') {
                filteredOrders = orders.filter(order => order.status_id === 3);
            }

            if (filteredOrders.length === 0) {
                container.style.display = 'none';
                document.getElementById('empty-state').style.display = 'block';
                return;
            }

            document.getElementById('empty-state').style.display = 'none';
            container.style.display = 'grid';

            container.innerHTML = filteredOrders.map(order => createOrderCard(order)).join('');

            // Add event listeners to order cards
            container.querySelectorAll('.order-card').forEach(card => {
                card.addEventListener('click', function() {
                    const orderId = this.dataset.orderId;
                    showOrderDetails(orderId);
                });
            });

            // Add event listeners to action buttons
            container.querySelectorAll('.start-preparing-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const orderId = this.dataset.orderId;
                    startPreparing(orderId);
                });
            });

            container.querySelectorAll('.mark-ready-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const orderId = this.dataset.orderId;
                    markAsReady(orderId);
                });
            });

            container.querySelectorAll('.notify-waiters-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const orderId = this.dataset.orderId;
                    notifyWaiters(orderId);
                });
            });

            container.querySelectorAll('.view-details-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const orderId = this.dataset.orderId;
                    showOrderDetails(orderId);
                });
            });
        }

        function createOrderCard(order) {
            const priority = order.priority || 'normal';
            const priorityClass = `priority-${priority.toLowerCase()}`;

            // Determinar status basado en status_id o status string
            let statusClass = 'status-pending';
            let statusText = 'Pendiente';

            if (order.status_id === 3 || order.status === 'preparing') {
                statusClass = 'status-preparing';
                statusText = 'Preparando';
            } else if (order.status === 'ready') {
                statusClass = 'status-ready';
                statusText = 'Listo';
            }

            const itemsCount = (order.items_summary || order.items || []).length;
            const totalAmount = order.total || '0.00';

            return `
                <div class="order-card ${priorityClass} bg-white rounded-lg shadow-md p-6 cursor-pointer hover:shadow-lg" data-order-id="${order.id}">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Orden #${order.id}</h3>
                            <p class="text-sm text-gray-600">${order.table_number || 'Mesa'} - ${order.table_name || 'Mesa ' + (order.table_id || '?')}</p>
                        </div>
                        <div class="text-right">
                            <span class="status-badge ${statusClass}">${statusText}</span>
                            <p class="text-xs text-gray-500 mt-1">${order.time_since_order || order.created_at || 'Hace poco'}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Artículos:</span>
                            <span class="font-semibold">${itemsCount}</span>
                        </div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Total:</span>
                            <span class="font-semibold">$${totalAmount}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Prioridad:</span>
                            <span class="font-semibold text-${getPriorityColor(priority)}">${priority}</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Platillos:</h4>
                        <div class="space-y-1">
                            ${(order.items_summary || order.items || []).slice(0, 3).map(item => `
                                <div class="text-xs text-gray-600">
                                    ${item.quantity}x ${item.product_name}
                                    ${item.notes ? `<em class="text-gray-500">- ${item.notes}</em>` : ''}
                                </div>
                            `).join('')}
                            ${(order.items_summary || order.items || []).length > 3 ? `<div class="text-xs text-gray-500">+${(order.items_summary || order.items || []).length - 3} más...</div>` : ''}
                        </div>
                    </div>

                    <div class="flex space-x-2">
                        ${order.status_id === 2 ? `
                            <button class="start-preparing-btn flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded text-sm font-medium" data-order-id="${order.id}">
                                <i class="fas fa-play mr-1"></i> Iniciar
                            </button>
                        ` : ''}

                        ${order.status_id === 3 ? `
                            <button class="mark-ready-btn flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded text-sm font-medium" data-order-id="${order.id}">
                                <i class="fas fa-check mr-1"></i> Finalizar
                            </button>
                        ` : ''}

                        <button class="view-details-btn flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded text-sm font-medium" data-order-id="${order.id}">
                            <i class="fas fa-eye mr-1"></i> Ver Detalles
                        </button>
                    </div>
                </div>
            `;
        }

        function getPriorityColor(priority) {
            const priorityStr = priority || 'normal';
            switch(priorityStr.toLowerCase()) {
                case 'urgente': return 'red-600';
                case 'alta': return 'orange-600';
                case 'media': return 'yellow-600';
                default: return 'green-600';
            }
        }

        async function showOrderDetails(orderId) {
            try {
                const response = await fetch(`/api/kitchen/orders/${orderId}/details`);
                const data = await response.json();

                if (data.success) {
                    renderOrderDetails(data.data);
                    document.getElementById('order-modal').classList.remove('hidden');
                } else {
                    showError('Error al cargar detalles de la orden');
                }
            } catch (error) {
                showError('Error de conexión: ' + error.message);
            }
        }

        function renderOrderDetails(order) {
            const modalContent = document.getElementById('modal-content');

            modalContent.innerHTML = `
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Order Info -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold">Información de la Orden</h3>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Orden #</label>
                                    <p class="text-lg font-semibold">${order.id}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Mesa</label>
                                    <p class="text-lg font-semibold">${order.table?.number || 'N/A'} - ${order.table?.name || 'Mesa'}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Estado</label>
                                    <p class="text-lg font-semibold">${order.status?.name || 'Pendiente'}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Prioridad</label>
                                    <p class="text-lg font-semibold text-${getPriorityColor(order.timing?.priority || 'normal')}">${order.timing?.priority || 'Normal'}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold mb-2">Tiempo</h4>
                            <div class="space-y-1">
                                <p class="text-sm"><strong>Creada:</strong> ${order.timing?.created_at || 'N/A'}</p>
                                <p class="text-sm"><strong>Tiempo transcurrido:</strong> ${order.timing?.time_since_order || 'N/A'}</p>
                                <p class="text-sm"><strong>Minutos esperando:</strong> ${order.timing?.waiting_minutes || '0'} min</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold mb-2">Información Adicional</h4>
                            <p class="text-sm"><strong>Mesero:</strong> ${order.customer_info?.waiter || 'N/A'}</p>
                            ${order.customer_info?.notes ? `<p class="text-sm"><strong>Notas:</strong> ${order.customer_info.notes}</p>` : ''}
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold">Platillos a Preparar</h3>

                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            ${(order.items || []).map(item => `
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-semibold text-lg">${item.product_name || item.product?.name || 'Producto'}</h4>
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-medium">x${item.quantity || 1}</span>
                                    </div>

                                    ${item.product?.description ? `<p class="text-sm text-gray-600 mb-2">${item.product.description}</p>` : ''}

                                    <div class="mb-2">
                                        <span class="text-xs font-medium text-gray-700">Categoría:</span>
                                        <span class="text-xs text-gray-600">${item.product?.category || 'N/A'}</span>
                                    </div>

                                    ${(item.product?.ingredients || []).length > 0 ? `
                                        <div class="mb-2">
                                            <span class="text-xs font-medium text-gray-700">Ingredientes:</span>
                                            <p class="text-xs text-gray-600">${item.product.ingredients.join(', ')}</p>
                                        </div>
                                    ` : ''}

                                    ${item.notes ? `
                                        <div class="mb-2">
                                            <span class="text-xs font-medium text-orange-700">Notas:</span>
                                            <p class="text-xs text-orange-600 font-medium">${item.notes}</p>
                                        </div>
                                    ` : ''}

                                    ${item.special_instructions ? `
                                        <div class="mb-2">
                                            <span class="text-xs font-medium text-red-700">Instrucciones Especiales:</span>
                                            <p class="text-xs text-red-600 font-medium">${item.special_instructions}</p>
                                        </div>
                                    ` : ''}

                                    <div class="flex justify-between text-sm">
                                        <span>Precio unitario: $${item.unit_price || item.price || '0.00'}</span>
                                        <span class="font-semibold">Total: $${item.total_price || item.subtotal || '0.00'}</span>
                                    </div>
                                </div>
                            `).join('')}
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="font-semibold mb-2">Totales</h4>
                            <div class="space-y-1">
                                <div class="flex justify-between"><span>Subtotal:</span><span>$${order.totals?.subtotal || order.total || '0.00'}</span></div>
                                <div class="flex justify-between"><span>Impuestos:</span><span>$${order.totals?.tax || '0.00'}</span></div>
                                <div class="flex justify-between font-semibold text-lg border-t pt-1"><span>Total:</span><span>$${order.totals?.total || order.total || '0.00'}</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4 mt-6 pt-6 border-t">
                    ${(order.status_id === 2 || order.status === 'pending' || order.status === 'Pending') ? `
                        <button class="start-preparing-btn flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-medium" data-order-id="${order.id}">
                            <i class="fas fa-play mr-2"></i> Iniciar Preparación
                        </button>
                    ` : ''}

                    ${(order.status_id === 3 || order.status === 'preparing' || order.status === 'Preparing') ? `
                        <button class="mark-ready-btn flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg font-medium" data-order-id="${order.id}">
                            <i class="fas fa-check mr-2"></i> Marcar como Listo
                        </button>
                    ` : ''}

                    ${(order.status_id === 4 || order.status === 'ready' || order.status === 'Ready') ? `
                        <button class="notify-waiters-btn flex-1 bg-yellow-600 hover:bg-yellow-700 text-white py-3 px-6 rounded-lg font-medium" data-order-id="${order.id}">
                            <i class="fas fa-bell mr-2"></i> Avisar Meseros
                        </button>
                    ` : ''}

                    <button onclick="closeModal()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-3 px-6 rounded-lg font-medium">
                        <i class="fas fa-times mr-2"></i> Cerrar
                    </button>
                </div>
            `;
        }

        async function startPreparing(orderId) {
            try {
                const response = await fetch(`/api/kitchen/orders/${orderId}/start-preparing`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showSuccess('Orden marcada como "En Preparación"');
                    closeModal();
                    loadOrders();
                    loadKitchenStats();
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Error al iniciar preparación: ' + error.message);
            }
        }

        async function markAsReady(orderId) {
            try {
                const response = await fetch(`/api/kitchen/orders/${orderId}/mark-ready`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showSuccess(data.data.notification);
                    closeModal();
                    loadOrders();
                    loadKitchenStats();
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Error al marcar como listo: ' + error.message);
            }
        }

        async function notifyWaiters(orderId) {
            try {
                const response = await fetch(`/api/kitchen/orders/${orderId}/notify-waiters`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showSuccess(data.message);
                    closeModal();
                    loadOrders();
                    loadKitchenStats();
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Error al notificar meseros: ' + error.message);
            }
        }

        function switchFilter(filter) {
            currentFilter = filter;

            // Update tab styles
            document.querySelectorAll('.filter-tab').forEach(tab => {
                if (tab.dataset.filter === filter) {
                    tab.classList.add('active', 'bg-blue-600', 'text-white');
                    tab.classList.remove('bg-gray-200', 'text-gray-700');
                } else {
                    tab.classList.remove('active', 'bg-blue-600', 'text-white');
                    tab.classList.add('bg-gray-200', 'text-gray-700');
                }
            });

            renderOrders();
        }

        function toggleAutoRefresh() {
            isAutoRefreshEnabled = !isAutoRefreshEnabled;

            const btn = document.getElementById('auto-refresh-btn');
            const icon = document.getElementById('refresh-icon');
            const text = document.getElementById('refresh-text');

            if (isAutoRefreshEnabled) {
                btn.classList.add('bg-blue-700');
                btn.classList.remove('bg-gray-600');
                icon.classList.add('auto-refresh');
                text.textContent = 'Auto';
                startAutoRefresh();
            } else {
                btn.classList.remove('bg-blue-700');
                btn.classList.add('bg-gray-600');
                icon.classList.remove('auto-refresh');
                text.textContent = 'Manual';
                stopAutoRefresh();
            }
        }

        function startAutoRefresh() {
            if (autoRefreshInterval) clearInterval(autoRefreshInterval);

            autoRefreshInterval = setInterval(() => {
                if (isAutoRefreshEnabled) {
                    loadOrders();
                    loadKitchenStats();
                }
            }, 10000); // Refresh every 10 seconds
        }

        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
            }
        }

        function updateOrderCounts() {
            const pendingCount = orders.filter(order => order.status_id === 2 || order.status === 'pending').length;
            const preparingCount = orders.filter(order => order.status_id === 3 || order.status === 'preparing').length;

            document.getElementById('pending-count').textContent = pendingCount;
            document.getElementById('preparing-count').textContent = preparingCount;
        }

        function updateStatsDisplay(stats) {
            document.getElementById('pending-count').textContent = stats.pending_count || 0;
            document.getElementById('preparing-count').textContent = stats.preparing_count || 0;
            document.getElementById('completed-today').textContent = stats.completed_today || 0;
        }

        function closeModal() {
            document.getElementById('order-modal').classList.add('hidden');
        }

        function showSuccess(message) {
            const notification = document.getElementById('success-notification');
            document.getElementById('success-message').textContent = message;
            notification.classList.remove('hidden');

            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);
        }

        function showError(message) {
            const notification = document.getElementById('error-notification');
            document.getElementById('error-message').textContent = message;
            notification.classList.remove('hidden');

            setTimeout(() => {
                notification.classList.add('hidden');
            }, 5000);
        }
    </script>
</body>
</html>
