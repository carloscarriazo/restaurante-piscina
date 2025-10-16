/**
 * Manejador de notificaciones en tiempo real
 * Sistema de notificaciones para el restaurante
 */
class NotificationManager {
    constructor() {
        this.notifications = [];
        this.unreadCount = 0;
        this.isConnected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectInterval = 5000;

        this.initializeEventListeners();
        // DESACTIVADO TEMPORALMENTE - Causaba conflictos con Livewire
        // this.connectToNotifications();
        console.log('⚠️ Sistema de notificaciones desactivado temporalmente');
    }

    /**
     * Inicializar event listeners para la interfaz
     */
    initializeEventListeners() {
        // Escuchar eventos de Livewire para notificaciones
        document.addEventListener('livewire:init', () => {
            Livewire.on('notification.kitchen', (data) => {
                this.handleKitchenNotification(data);
            });

            Livewire.on('notification.inventory', (data) => {
                this.handleInventoryAlert(data);
            });

            Livewire.on('notification.order', (data) => {
                this.handleOrderUpdate(data);
            });

            Livewire.on('show-toast', (data) => {
                this.showToast(data.type, data.title, data.message);
            });
        });

        // Manejar visibilidad de la página para reconectar
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && !this.isConnected) {
                this.connectToNotifications();
            }
        });
    }

    /**
     * Conectar al sistema de notificaciones
     */
    connectToNotifications() {
        // Verificar si Laravel Echo está disponible
        if (typeof Echo !== 'undefined') {
            this.connectWithEcho();
        } else {
            // Fallback a polling si Echo no está disponible
            this.connectWithPolling();
        }
    }

    /**
     * Conectar usando Laravel Echo (WebSockets)
     */
    connectWithEcho() {
        try {
            // Escuchar canal general de cocina
            Echo.channel('kitchen-updates')
                .listen('KitchenOrderReady', (data) => {
                    this.handleKitchenNotification(data);
                });

            // Escuchar alertas de inventario
            Echo.channel('inventory-alerts')
                .listen('LowStockAlert', (data) => {
                    this.handleInventoryAlert(data);
                });

            // Escuchar actualizaciones de pedidos
            Echo.channel('order-updates')
                .listen('OrderStatusChanged', (data) => {
                    this.handleOrderUpdate(data);
                });

            // Canal privado del usuario (si está autenticado)
            if (window.currentUserId) {
                Echo.private(`user.${window.currentUserId}`)
                    .listen('KitchenOrderReady', (data) => {
                        this.handleKitchenNotification(data);
                    })
                    .listen('OrderStatusChanged', (data) => {
                        this.handleOrderUpdate(data);
                    });
            }

            this.isConnected = true;
            this.reconnectAttempts = 0;
            console.log('✅ Conectado a notificaciones en tiempo real');

        } catch (error) {
            console.error('❌ Error conectando a Echo:', error);
            this.connectWithPolling();
        }
    }

    /**
     * Conectar usando polling (fallback)
     */
    connectWithPolling() {
        console.log('🔄 Usando polling para notificaciones...');

        this.pollingInterval = setInterval(async () => {
            try {
                const response = await fetch('/api/notifications/realtime?timeout=30', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${this.getAuthToken()}`,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();

                    if (data.success && data.data.has_new) {
                        data.data.notifications.forEach(notification => {
                            this.processNotification(notification);
                        });
                    }

                    this.isConnected = true;
                    this.reconnectAttempts = 0;
                } else {
                    throw new Error(`HTTP ${response.status}`);
                }

            } catch (error) {
                console.error('Error en polling:', error);
                this.handleConnectionError();
            }
        }, 10000); // Polling cada 10 segundos
    }

    /**
     * Manejar notificación de cocina
     */
    handleKitchenNotification(data) {
        const notification = {
            id: Date.now(),
            type: 'kitchen_ready',
            title: '🍽️ Pedido Listo',
            message: `Pedido ${data.order_code} listo en la ${data.table_name}`,
            timestamp: new Date(),
            data: data
        };

        this.addNotification(notification);
        this.showToast('success', '🍽️ Pedido Listo', notification.message);
        this.playNotificationSound('kitchen');

        // Actualizar componentes Livewire
        if (typeof Livewire !== 'undefined') {
            Livewire.dispatch('notification.new');
        }
    }

    /**
     * Manejar alerta de inventario
     */
    handleInventoryAlert(data) {
        const notification = {
            id: Date.now(),
            type: 'low_stock',
            title: '⚠️ Stock Bajo',
            message: `Stock bajo: ${data.ingredient_name} (${data.current_stock} ${data.unit})`,
            timestamp: new Date(),
            data: data
        };

        this.addNotification(notification);
        this.showToast('warning', '⚠️ Alerta de Stock', notification.message);
        this.playNotificationSound('alert');
    }

    /**
     * Manejar actualización de pedido
     */
    handleOrderUpdate(data) {
        const notification = {
            id: Date.now(),
            type: 'order_update',
            title: '📋 Pedido Actualizado',
            message: `Pedido ${data.order_code}: ${data.status}`,
            timestamp: new Date(),
            data: data
        };

        this.addNotification(notification);
        this.showToast('info', '📋 Actualización', notification.message);
    }

    /**
     * Procesar notificación genérica
     */
    processNotification(notification) {
        switch (notification.type) {
            case 'kitchen_ready':
                this.handleKitchenNotification(notification.data ? JSON.parse(notification.data) : {});
                break;
            case 'low_stock':
                this.handleInventoryAlert(notification.data ? JSON.parse(notification.data) : {});
                break;
            case 'order_update':
                this.handleOrderUpdate(notification.data ? JSON.parse(notification.data) : {});
                break;
            default:
                this.showToast('info', notification.title, notification.message);
        }
    }

    /**
     * Agregar notificación al array
     */
    addNotification(notification) {
        this.notifications.unshift(notification);
        this.unreadCount++;

        // Mantener solo las últimas 50 notificaciones
        if (this.notifications.length > 50) {
            this.notifications = this.notifications.slice(0, 50);
        }

        this.updateNotificationUI();
    }

    /**
     * Mostrar toast notification
     */
    showToast(type, title, message) {
        // Dispatch evento personalizado para que Alpine.js lo maneje
        window.dispatchEvent(new CustomEvent('show-toast', {
            detail: { type, title, message }
        }));
    }

    /**
     * Reproducir sonido de notificación
     */
    playNotificationSound(type = 'default') {
        if (!this.soundEnabled()) return;

        try {
            const audio = new Audio();

            switch (type) {
                case 'kitchen':
                    audio.src = '/sounds/kitchen-ready.mp3';
                    break;
                case 'alert':
                    audio.src = '/sounds/alert.mp3';
                    break;
                default:
                    audio.src = '/sounds/notification.mp3';
            }

            audio.volume = 0.7;
            audio.play().catch(() => {
                // Silenciar error si el usuario no ha interactuado con la página
            });
        } catch (error) {
            console.warn('No se pudo reproducir sonido de notificación:', error);
        }
    }

    /**
     * Verificar si los sonidos están habilitados
     */
    soundEnabled() {
        return localStorage.getItem('notifications-sound') !== 'false';
    }

    /**
     * Actualizar UI de notificaciones
     */
    updateNotificationUI() {
        // Actualizar badge de notificaciones
        const badge = document.querySelector('[data-notification-badge]');
        if (badge) {
            badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
            badge.style.display = this.unreadCount > 0 ? 'inline' : 'none';
        }

        // Actualizar título de la página si hay notificaciones
        this.updatePageTitle();
    }

    /**
     * Actualizar título de la página
     */
    updatePageTitle() {
        const originalTitle = document.title.replace(/^\(\d+\) /, '');

        if (this.unreadCount > 0) {
            document.title = `(${this.unreadCount}) ${originalTitle}`;
        } else {
            document.title = originalTitle;
        }
    }

    /**
     * Obtener token de autenticación
     */
    getAuthToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
               localStorage.getItem('auth_token') ||
               '';
    }

    /**
     * Manejar errores de conexión
     */
    handleConnectionError() {
        this.isConnected = false;
        this.reconnectAttempts++;

        if (this.reconnectAttempts <= this.maxReconnectAttempts) {
            console.log(`🔄 Reintentando conexión (${this.reconnectAttempts}/${this.maxReconnectAttempts})...`);

            setTimeout(() => {
                this.connectToNotifications();
            }, this.reconnectInterval * this.reconnectAttempts);
        } else {
            console.error('❌ Máximo de reintentos alcanzado. Verifique la conexión.');
            this.showToast('error', 'Error de Conexión', 'No se pudo conectar al servidor de notificaciones');
        }
    }

    /**
     * Marcar notificación como leída
     */
    async markAsRead(notificationId) {
        try {
            const response = await fetch(`/api/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });

            if (response.ok) {
                this.unreadCount = Math.max(0, this.unreadCount - 1);
                this.updateNotificationUI();
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    /**
     * Obtener estadísticas de notificaciones
     */
    async getStats() {
        try {
            const response = await fetch('/api/notifications/stats', {
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`
                }
            });

            if (response.ok) {
                const data = await response.json();
                return data.success ? data.data : {};
            }
        } catch (error) {
            console.error('Error getting notification stats:', error);
        }

        return {};
    }

    /**
     * Destructor - limpiar recursos
     */
    destroy() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }

        if (typeof Echo !== 'undefined') {
            Echo.leaveChannel('kitchen-updates');
            Echo.leaveChannel('inventory-alerts');
            Echo.leaveChannel('order-updates');

            if (window.currentUserId) {
                Echo.leaveChannel(`user.${window.currentUserId}`);
            }
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Solo inicializar si el usuario está autenticado
    if (document.querySelector('meta[name="user-authenticated"]')) {
        window.notificationManager = new NotificationManager();
    }
});

// Limpiar al cerrar la página
window.addEventListener('beforeunload', () => {
    if (window.notificationManager) {
        window.notificationManager.destroy();
    }
});
