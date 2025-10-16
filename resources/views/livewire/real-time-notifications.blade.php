<!-- Wrapper único para cumplir con Livewire single root requirement -->
<div>
    <div class="relative">
        <!-- Icono de notificaciones -->
        <button wire:click="toggleNotifications"
                class="relative p-2 text-gray-600 hover:text-gray-800 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                </path>
            </svg>

        <!-- Badge de notificaciones no leídas -->
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Panel de notificaciones -->
    @if($showNotifications)
        <div class="absolute right-0 z-50 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Notificaciones</h3>
                <div class="flex space-x-2">
                    @if($unreadCount > 0)
                        <button wire:click="markAllAsRead"
                                class="text-sm text-blue-600 hover:text-blue-800">
                            Marcar todas como leídas
                        </button>
                    @endif
                    <button wire:click="clearAllNotifications"
                            class="text-sm text-red-600 hover:text-red-800">
                        Limpiar todas
                    </button>
                </div>
            </div>

            <!-- Lista de notificaciones -->
            <div class="max-h-96 overflow-y-auto">
                @forelse($notifications as $notification)
                    <div class="p-4 border-b border-gray-100 hover:bg-gray-50 {{ !$notification['read_at'] ? 'bg-blue-50' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-semibold text-gray-900 {{ !$notification['read_at'] ? 'font-bold' : '' }}">
                                        {{ $notification['title'] }}
                                    </h4>
                                    <div class="flex items-center space-x-2 ml-2">
                                        <!-- Indicador de tipo -->
                                        @switch($notification['type'])
                                            @case('kitchen_ready')
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                    Cocina
                                                </span>
                                                @break
                                            @case('low_stock')
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                    Stock
                                                </span>
                                                @break
                                            @case('order_update')
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                    Pedido
                                                </span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                                    General
                                                </span>
                                        @endswitch

                                        <!-- Botón eliminar -->
                                        <button wire:click="clearNotification({{ $notification['id'] }})"
                                                class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <p class="mt-1 text-sm text-gray-600">
                                    {{ $notification['message'] }}
                                </p>

                                <div class="mt-2 flex items-center justify-between">
                                    <span class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                                    </span>

                                    @if(!$notification['read_at'])
                                        <button wire:click="markAsRead({{ $notification['id'] }})"
                                                class="text-xs text-blue-600 hover:text-blue-800">
                                            Marcar como leída
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
                        </svg>
                        <p class="text-sm">No tienes notificaciones</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
    </div>

    <!-- Toast notifications -->
    <div x-data="{
    show: false,
    type: '',
    title: '',
    message: '',
    timeout: null
}"
     x-on:show-toast.window="
        show = true;
        type = $event.detail.type;
        title = $event.detail.title;
        message = $event.detail.message;
        clearTimeout(timeout);
        timeout = setTimeout(() => show = false, 5000);
     "
     x-show="show"
     x-transition:enter="transform ease-out duration-300 transition"
     x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
     x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
     x-transition:leave="transition ease-in duration-100"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed top-4 right-4 z-50 w-96 max-w-sm bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5"
     style="display: none;">

    <div class="p-4">
        <div class="flex items-start">
            <!-- Icono según tipo -->
            <div class="flex-shrink-0">
                <template x-if="type === 'success'">
                    <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
                <template x-if="type === 'warning'">
                    <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </template>
                <template x-if="type === 'info'">
                    <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
            </div>

            <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-medium text-gray-900" x-text="title"></p>
                <p class="mt-1 text-sm text-gray-500" x-text="message"></p>
            </div>

            <div class="ml-4 flex-shrink-0 flex">
                <button @click="show = false"
                        class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    </div>
</div>
<!-- Fin del wrapper único de Livewire -->
