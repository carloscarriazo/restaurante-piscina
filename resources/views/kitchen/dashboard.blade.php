<x-app-layout>
    <x-slot name="title">Panel de Cocina</x-slot>

    @push('styles')
    <style>
        .ocean-kitchen-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .ocean-kitchen-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(6, 182, 212, 0.3);
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
            display: inline-block;
            padding: 0.375rem 0.875rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .status-pending {
            background: rgba(251, 191, 36, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .status-preparing {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .status-ready {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .ocean-stat-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .ocean-stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(6, 182, 212, 0.3);
        }

        .ocean-stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #06b6d4, #0ea5e9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .filter-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .filter-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #06b6d4, #0ea5e9);
            border-color: rgba(6, 182, 212, 0.5);
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
        }

        .ocean-btn-action {
            padding: 0.625rem 1.25rem;
            border-radius: 0.75rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .ocean-btn-primary {
            background: linear-gradient(135deg, #06b6d4, #0ea5e9);
            color: white;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);
        }

        .ocean-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(6, 182, 212, 0.5);
        }

        .ocean-btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .ocean-btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
        }

        .order-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            border-left: 3px solid #06b6d4;
        }

        @media (max-width: 768px) {
            .ocean-stat-value {
                font-size: 2rem;
            }
        }
    </style>
    @endpush

    <!-- Header con Estadísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="ocean-stat-box">
            <i class="fas fa-clock text-yellow-400 text-3xl mb-3"></i>
            <div class="ocean-stat-value" id="pending-count">0</div>
            <div class="text-gray-300 font-medium">Pendientes</div>
        </div>
        <div class="ocean-stat-box">
            <i class="fas fa-fire text-orange-400 text-3xl mb-3"></i>
            <div class="ocean-stat-value" id="preparing-count">0</div>
            <div class="text-gray-300 font-medium">En Preparación</div>
        </div>
        <div class="ocean-stat-box">
            <i class="fas fa-check-circle text-green-400 text-3xl mb-3"></i>
            <div class="ocean-stat-value" id="completed-today">0</div>
            <div class="text-gray-300 font-medium">Completadas Hoy</div>
        </div>
        <div class="ocean-stat-box">
            <i class="fas fa-stopwatch text-cyan-400 text-3xl mb-3"></i>
            <div class="ocean-stat-value" id="avg-time">0</div>
            <div class="text-gray-300 font-medium">Tiempo Promedio (min)</div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="flex flex-wrap gap-4 mb-8 items-center">
        <label class="text-white font-semibold mr-4">Filtrar:</label>
        <div class="flex gap-4" id="order-filters">
            <label class="flex items-center gap-2">
                <input type="radio" name="orderFilter" value="all" checked onchange="filterOrdersAuto(this.value)">
                <span class="filter-btn">Todas</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="radio" name="orderFilter" value="pending" onchange="filterOrdersAuto(this.value)">
                <span class="filter-btn">Pendientes</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="radio" name="orderFilter" value="preparing" onchange="filterOrdersAuto(this.value)">
                <span class="filter-btn">En Preparación</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="radio" name="orderFilter" value="ready" onchange="filterOrdersAuto(this.value)">
                <span class="filter-btn">Listas</span>
            </label>
        </div>
    </div>

    <!-- Grid de Órdenes -->
    <div id="orders-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Las órdenes se cargarán aquí dinámicamente -->
    </div>

    <!-- Estado de Carga -->
    <div id="loading-state" class="text-center py-12">
        <i class="fas fa-spinner fa-spin text-4xl text-cyan-400 mb-4"></i>
        <p class="text-gray-300 text-lg">Cargando órdenes...</p>
    </div>

    <!-- Estado Vacío -->
    <div id="empty-state" class="text-center py-12 hidden">
        <i class="fas fa-clipboard-list text-6xl text-gray-500 mb-4"></i>
        <h3 class="text-2xl font-bold text-white mb-2">No hay órdenes pendientes</h3>
        <p class="text-gray-300">¡Excelente trabajo! Todas las órdenes están completadas.</p>
    </div>

    <!-- Modal de Detalles (se agregará con JavaScript) -->
    <div id="order-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeModal()">
        <div class="ocean-kitchen-card max-w-4xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/20">
                <h2 class="text-2xl font-bold text-white">Detalles de la Orden</h2>
                <button onclick="closeModal()" class="text-gray-400 hover:text-white text-2xl transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="modal-content">
                <!-- Contenido del modal se cargará aquí -->
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let allOrders = [];
        let currentFilter = 'all';

        // Cargar órdenes al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            loadOrders();
            loadStats();

            // Auto-refresh cada 30 segundos
            setInterval(() => {
                loadOrders();
                loadStats();
            }, 30000);
        });

        async function loadOrders() {
            try {
                const response = await fetch('/api/kitchen/orders');
                const result = await response.json();
                
                console.log('Respuesta API:', result); // Debug
                
                if (result.success) {
                    allOrders = result.data || [];
                    console.log('Órdenes cargadas:', allOrders.length); // Debug
                } else {
                    allOrders = [];
                    console.error('Error en respuesta:', result.message);
                }
                
                renderOrders();
                document.getElementById('loading-state').classList.add('hidden');
            } catch (error) {
                console.error('Error cargando órdenes:', error);
                document.getElementById('loading-state').innerHTML = `
                    <i class="fas fa-exclamation-triangle text-4xl text-red-400 mb-4"></i>
                    <p class="text-gray-300">Error al cargar las órdenes</p>
                `;
            }
        }

        async function loadStats() {
            try {
                const response = await fetch('/api/kitchen/stats');
                const result = await response.json();

                if (result.success && result.data) {
                    const data = result.data;
                    document.getElementById('pending-count').textContent = data.pending_count || 0;
                    document.getElementById('preparing-count').textContent = data.preparing_count || 0;
                    document.getElementById('completed-today').textContent = data.completed_today || 0;
                    document.getElementById('avg-time').textContent = data.avg_preparation_time || 0;
                }
            } catch (error) {
                console.error('Error cargando estadísticas:', error);
            }
        }

        function filterOrdersAuto(filter) {
            currentFilter = filter;
            renderOrders();
        }

        function renderOrders() {
            const container = document.getElementById('orders-container');
            const emptyState = document.getElementById('empty-state');

            let filteredOrders = allOrders;

            if (currentFilter !== 'all') {
                // Mapear filtros a estados de BD
                const filterMap = {
                    'pending': 'pending',
                    'preparing': 'in_process',
                    'ready': 'ready'
                };
                const dbStatus = filterMap[currentFilter] || currentFilter;
                filteredOrders = allOrders.filter(order => order.status === dbStatus);
            }

            if (filteredOrders.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');

            container.innerHTML = filteredOrders.map(order => `
                <div class="ocean-kitchen-card priority-${order.priority || 'normal'}" onclick="showOrderDetails(${order.id})">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-white mb-1">Orden #${order.id}</h3>
                            <p class="text-gray-300 text-sm">
                                <i class="fas fa-table mr-1"></i>
                                Mesa ${order.table_number || 'N/A'}
                            </p>
                        </div>
                        <span class="status-badge status-${order.status === 'in_process' ? 'preparing' : order.status}">
                            ${getStatusText(order.status)}
                        </span>
                    </div>

                    <div class="space-y-2 mb-4">
                        ${(order.items || []).map(item => `
                            <div class="order-item">
                                <div class="flex justify-between items-start">
                                    <span class="text-white font-medium">${item.quantity}x ${item.name}</span>
                                    <span class="text-cyan-300 text-sm">${formatPrice(item.price)}</span>
                                </div>
                                ${item.notes ? `<p class="text-gray-400 text-sm mt-1">${item.notes}</p>` : ''}
                            </div>
                        `).join('')}
                    </div>

                    <div class="flex items-center justify-between text-sm text-gray-400 mb-4">
                        <span>
                            <i class="fas fa-clock mr-1"></i>
                            ${formatTime(order.created_at)}
                        </span>
                        <span class="text-white font-bold">
                            Total: ${formatPrice(order.total)}
                        </span>
                    </div>

                    <div class="flex gap-2">
                        ${order.status === 'pending' ? `
                            <button onclick="event.stopPropagation(); startPreparing(${order.id})"
                                    class="ocean-btn-action ocean-btn-primary flex-1">
                                <i class="fas fa-play"></i>
                                Iniciar
                            </button>
                        ` : ''}
                        ${order.status === 'in_process' ? `
                            <button onclick="event.stopPropagation(); markAsReady(${order.id})"
                                    class="ocean-btn-action ocean-btn-success flex-1">
                                <i class="fas fa-check"></i>
                                Marcar Lista
                            </button>
                        ` : ''}
                    </div>
                </div>
            `).join('');
        }

        async function startPreparing(orderId) {
            try {
                const response = await fetch(`/api/kitchen/orders/${orderId}/start-preparing`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    showNotification('Orden iniciada', 'success');
                    loadOrders();
                    loadStats();
                }
            } catch (error) {
                showNotification('Error al iniciar orden', 'error');
            }
        }

        async function markAsReady(orderId) {
            try {
                const response = await fetch(`/api/kitchen/orders/${orderId}/mark-ready`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    showNotification('Orden marcada como lista', 'success');
                    loadOrders();
                    loadStats();
                }
            } catch (error) {
                showNotification('Error al marcar orden', 'error');
            }
        }

        async function showOrderDetails(orderId) {
            const modal = document.getElementById('order-modal');
            const modalContent = document.getElementById('modal-content');

            modalContent.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-cyan-400"></i></div>';
            modal.classList.remove('hidden');

            try {
                const response = await fetch(`/api/kitchen/orders/${orderId}/details`);
                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.message || 'Error al cargar detalles');
                }
                
                const order = result.data;

                modalContent.innerHTML = `
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-gray-400 text-sm">Orden</label>
                                <p class="text-white text-xl font-bold">#${order.id}</p>
                            </div>
                            <div>
                                <label class="text-gray-400 text-sm">Mesa</label>
                                <p class="text-white text-xl font-bold">${order.table?.number || order.table_number || 'N/A'}</p>
                            </div>
                            <div>
                                <label class="text-gray-400 text-sm">Estado</label>
                                <p class="text-white"><span class="status-badge status-${order.status?.name?.toLowerCase() || order.status}">${getStatusText(order.status?.name?.toLowerCase() || order.status)}</span></p>
                            </div>
                            <div>
                                <label class="text-gray-400 text-sm">Hora</label>
                                <p class="text-white">${formatTime(order.timing?.created_at || order.created_at)}</p>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-white font-bold mb-3">Items de la Orden</h3>
                            <div class="space-y-2">
                                ${(order.items || []).map(item => `
                                    <div class="order-item">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="text-white font-medium">${item.quantity}x ${item.product_name || item.name}</span>
                                            <span class="text-cyan-300">${formatPrice(item.total_price || (item.price * item.quantity))}</span>
                                        </div>
                                        ${item.notes ? `<p class="text-gray-400 text-sm">${item.notes}</p>` : ''}
                                        ${item.special_instructions ? `<p class="text-yellow-400 text-sm">⚠️ ${item.special_instructions}</p>` : ''}
                                    </div>
                                `).join('')}
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-white/20">
                            <span class="text-xl font-bold text-white">Total</span>
                            <span class="text-2xl font-bold text-cyan-300">${formatPrice(order.total)}</span>
                        </div>
                        
                        <div class="flex gap-3 pt-4">
                            ${order.status === 'pending' || order.status_id === 2 ? `
                                <button onclick="startPreparing(${order.id}); closeModal();"
                                        class="ocean-btn-action ocean-btn-primary flex-1">
                                    <i class="fas fa-play"></i>
                                    Iniciar Preparación
                                </button>
                            ` : ''}
                            ${order.status === 'in_process' || order.status_id === 3 ? `
                                <button onclick="markAsReady(${order.id}); closeModal();"
                                        class="ocean-btn-action ocean-btn-success flex-1">
                                    <i class="fas fa-check"></i>
                                    Marcar como Lista
                                </button>
                            ` : ''}
                            <button onclick="closeModal()" class="ocean-btn-action" style="background: rgba(255,255,255,0.1);">
                                <i class="fas fa-times"></i>
                                Cerrar
                            </button>
                        </div>
                    </div>
                `;
            } catch (error) {
                console.error('Error:', error);
                modalContent.innerHTML = '<p class="text-red-400 text-center py-8">Error al cargar detalles: ' + error.message + '</p>';
            }
        }

        function closeModal() {
            document.getElementById('order-modal').classList.add('hidden');
        }

        function getStatusText(status) {
            const statuses = {
                'pending': 'Pendiente',
                'in_process': 'Preparando',
                'preparing': 'Preparando',
                'ready': 'Lista',
                'completed': 'Completada',
                'served': 'Servida'
            };
            return statuses[status] || status;
        }

        function formatPrice(amount) {
            return '$' + parseFloat(amount).toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        function formatTime(datetime) {
            // Si ya viene como string de hora (HH:mm), retornarlo directamente
            if (typeof datetime === 'string' && datetime.match(/^\d{2}:\d{2}$/)) {
                return datetime;
            }
            const date = new Date(datetime);
            return date.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
        }

        function showNotification(message, type) {
            // Simple notification usando alert por ahora
            // Puedes mejorar esto con una librería de notificaciones
            if (type === 'success') {
                alert('✅ ' + message);
            } else {
                alert('❌ ' + message);
            }
        }

        function showNotification(message, type) {
            // Puedes implementar un sistema de notificaciones más elaborado
            alert(message);
        }
    </script>
    @endpush
</x-app-layout>
