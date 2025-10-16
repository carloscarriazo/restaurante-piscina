<div class="min-h-screen bg-gradient-to-br from-ocean-950 via-ocean-900 to-blue-950"><div>

    <!-- Header -->    {{-- Nothing in the world is as soft and yielding as water. --}}

    <div class="bg-gradient-to-r from-ocean-600 via-ocean-500 to-blue-500 shadow-ocean"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                        <i class="fas fa-chart-line"></i>
                        Reportes y Estadísticas
                    </h1>
                    <p class="text-ocean-100 mt-1">Panel completo de análisis y métricas del restaurante</p>
                </div>
                <div class="flex gap-3">
                    <button wire:click="exportReport" class="btn-ocean">
                        <i class="fas fa-download mr-2"></i>
                        Exportar
                    </button>
                    <button wire:click="printReport" class="btn-ocean-secondary">
                        <i class="fas fa-print mr-2"></i>
                        Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filtros de Fecha -->
        <div class="card-ocean mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-ocean-200 mb-2">
                        <i class="fas fa-calendar mr-2"></i>
                        Período
                    </label>
                    <select wire:model.live="dateFilter" class="input-ocean w-full">
                        <option value="today">Hoy</option>
                        <option value="week">Esta Semana</option>
                        <option value="month">Este Mes</option>
                        <option value="custom">Personalizado</option>
                    </select>
                </div>

                @if($dateFilter === 'custom')
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-ocean-200 mb-2">
                            Desde
                        </label>
                        <input type="date" wire:model.live="startDate" class="input-ocean w-full">
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-ocean-200 mb-2">
                            Hasta
                        </label>
                        <input type="date" wire:model.live="endDate" class="input-ocean w-full">
                    </div>
                @endif

                <div class="pt-6">
                    <span class="text-ocean-300 text-sm">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="card-ocean mb-6">
            <div class="flex flex-wrap gap-2 border-b border-ocean-700 pb-4">
                <button
                    wire:click="$set('activeTab', 'overview')"
                    class="px-6 py-3 rounded-lg font-medium transition-all duration-300 {{ $activeTab === 'overview' ? 'bg-ocean-500 text-white shadow-lg' : 'text-ocean-300 hover:bg-ocean-800' }}">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Resumen General
                </button>
                <button
                    wire:click="$set('activeTab', 'products')"
                    class="px-6 py-3 rounded-lg font-medium transition-all duration-300 {{ $activeTab === 'products' ? 'bg-ocean-500 text-white shadow-lg' : 'text-ocean-300 hover:bg-ocean-800' }}">
                    <i class="fas fa-fire mr-2"></i>
                    Productos Más Vendidos
                </button>
                <button
                    wire:click="$set('activeTab', 'invoices')"
                    class="px-6 py-3 rounded-lg font-medium transition-all duration-300 {{ $activeTab === 'invoices' ? 'bg-ocean-500 text-white shadow-lg' : 'text-ocean-300 hover:bg-ocean-800' }}">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>
                    Facturas
                </button>
                <button
                    wire:click="$set('activeTab', 'services')"
                    class="px-6 py-3 rounded-lg font-medium transition-all duration-300 {{ $activeTab === 'services' ? 'bg-ocean-500 text-white shadow-lg' : 'text-ocean-300 hover:bg-ocean-800' }}">
                    <i class="fas fa-concierge-bell mr-2"></i>
                    Servicios Disponibles
                </button>
            </div>
        </div>

        <!-- Contenido de Tabs -->
        @if($activeTab === 'overview')
            <!-- KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Ventas -->
                <div class="card-ocean hover-scale">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-ocean-300 text-sm font-medium">Total Ventas</p>
                            <p class="text-3xl font-bold text-white mt-2">
                                ${{ number_format($overviewData['total_sales'], 2) }}
                            </p>
                            <p class="text-ocean-400 text-xs mt-1">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $dateFilter === 'today' ? 'Hoy' : 'Período seleccionado' }}
                            </p>
                        </div>
                        <div class="w-16 h-16 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-3xl text-green-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Órdenes -->
                <div class="card-ocean hover-scale">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-ocean-300 text-sm font-medium">Total Órdenes</p>
                            <p class="text-3xl font-bold text-white mt-2">
                                {{ $overviewData['total_orders'] }}
                            </p>
                            <p class="text-ocean-400 text-xs mt-1">
                                <i class="fas fa-shopping-cart mr-1"></i>
                                Órdenes completadas
                            </p>
                        </div>
                        <div class="w-16 h-16 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-receipt text-3xl text-blue-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Promedio por Orden -->
                <div class="card-ocean hover-scale">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-ocean-300 text-sm font-medium">Promedio/Orden</p>
                            <p class="text-3xl font-bold text-white mt-2">
                                ${{ number_format($overviewData['average_order'], 2) }}
                            </p>
                            <p class="text-ocean-400 text-xs mt-1">
                                <i class="fas fa-chart-line mr-1"></i>
                                Ticket promedio
                            </p>
                        </div>
                        <div class="w-16 h-16 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calculator text-3xl text-purple-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Facturas -->
                <div class="card-ocean hover-scale">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-ocean-300 text-sm font-medium">Total Facturas</p>
                            <p class="text-3xl font-bold text-white mt-2">
                                {{ $overviewData['total_invoices'] }}
                            </p>
                            <p class="text-ocean-400 text-xs mt-1">
                                <i class="fas fa-file-invoice mr-1"></i>
                                Facturas generadas
                            </p>
                        </div>
                        <div class="w-16 h-16 bg-orange-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-file-invoice-dollar text-3xl text-orange-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfica de Ventas por Día -->
            <div class="card-ocean mb-8">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-ocean-400"></i>
                    Ventas de los Últimos 7 Días
                </h3>
                <div class="space-y-4">
                    @foreach($overviewData['sales_by_day'] as $day)
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-ocean-300 text-sm font-medium">
                                    {{ Carbon\Carbon::parse($day->date)->isoFormat('dddd D') }}
                                </span>
                                <span class="text-white font-bold">
                                    ${{ number_format($day->total, 2) }}
                                </span>
                            </div>
                            <div class="w-full bg-ocean-800 rounded-full h-4 overflow-hidden">
                                <div
                                    class="bg-gradient-to-r from-ocean-500 to-blue-500 h-4 rounded-full transition-all duration-500"
                                    style="width: {{ $overviewData['sales_by_day']->max('total') > 0 ? ($day->total / $overviewData['sales_by_day']->max('total') * 100) : 0 }}%">
                                </div>
                            </div>
                            <span class="text-ocean-400 text-xs">{{ $day->count }} órdenes</span>
                        </div>
                    @endforeach

                    @if($overviewData['sales_by_day']->isEmpty())
                        <div class="text-center text-ocean-400 py-8">
                            <i class="fas fa-chart-bar text-4xl mb-4"></i>
                            <p>No hay datos de ventas en este período</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Métricas Adicionales -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card-ocean">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-2xl text-yellow-400"></i>
                        </div>
                        <div>
                            <p class="text-ocean-300 text-sm">Órdenes Pendientes</p>
                            <p class="text-2xl font-bold text-white">{{ $overviewData['pending_orders'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-ocean">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chair text-2xl text-red-400"></i>
                        </div>
                        <div>
                            <p class="text-ocean-300 text-sm">Mesas Ocupadas</p>
                            <p class="text-2xl font-bold text-white">{{ $overviewData['occupied_tables'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-ocean">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-2xl text-green-400"></i>
                        </div>
                        <div>
                            <p class="text-ocean-300 text-sm">Ingresos Facturas</p>
                            <p class="text-2xl font-bold text-white">${{ number_format($overviewData['invoice_revenue'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'products')
            <div class="card-ocean">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-fire text-orange-400"></i>
                    Top 10 Productos Más Vendidos
                </h3>

                @if($topProducts->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-ocean-700">
                                    <th class="text-left py-4 px-4 text-ocean-300 font-semibold">#</th>
                                    <th class="text-left py-4 px-4 text-ocean-300 font-semibold">Producto</th>
                                    <th class="text-center py-4 px-4 text-ocean-300 font-semibold">Cantidad Vendida</th>
                                    <th class="text-center py-4 px-4 text-ocean-300 font-semibold">Ingresos</th>
                                    <th class="text-center py-4 px-4 text-ocean-300 font-semibold">Órdenes</th>
                                    <th class="text-right py-4 px-4 text-ocean-300 font-semibold">Precio Unit.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $index => $product)
                                    <tr class="border-b border-ocean-800 hover:bg-ocean-800/50 transition-colors">
                                        <td class="py-4 px-4">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                                {{ $index === 0 ? 'bg-yellow-500/20 text-yellow-400' : ($index === 1 ? 'bg-gray-400/20 text-gray-300' : ($index === 2 ? 'bg-orange-500/20 text-orange-400' : 'bg-ocean-700 text-ocean-300')) }}
                                                font-bold text-sm">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="font-semibold text-white">{{ $product->name }}</div>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-500/20 text-blue-400 font-semibold">
                                                {{ $product->total_quantity }} unidades
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span class="text-green-400 font-bold text-lg">
                                                ${{ number_format($product->total_revenue, 2) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center text-ocean-300">
                                            {{ $product->order_count }}
                                        </td>
                                        <td class="py-4 px-4 text-right text-ocean-300">
                                            ${{ number_format($product->price, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-ocean-400 py-12">
                        <i class="fas fa-box-open text-5xl mb-4"></i>
                        <p class="text-lg">No hay datos de productos en este período</p>
                    </div>
                @endif
            </div>
        @endif

        @if($activeTab === 'invoices')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Facturas del Día -->
                <div class="card-ocean">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-calendar-day text-ocean-400"></i>
                            Facturas de Hoy
                        </h3>
                        <div class="text-right">
                            <p class="text-sm text-ocean-300">Ingresos del Día</p>
                            <p class="text-2xl font-bold text-green-400">${{ number_format($dailyRevenue, 2) }}</p>
                        </div>
                    </div>

                    <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                        @forelse($dailyInvoices as $invoice)
                            <div class="bg-ocean-800/50 rounded-lg p-4 hover:bg-ocean-800 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-mono text-ocean-300 text-sm">#{{ $invoice->invoice_number }}</span>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $invoice->status === 'paid' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                        {{ $invoice->status === 'paid' ? 'Pagada' : 'Cancelada' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-white font-semibold">Mesa {{ $invoice->order->table->number ?? 'N/A' }}</p>
                                        <p class="text-ocean-400 text-xs">{{ $invoice->created_at->format('H:i') }}</p>
                                    </div>
                                    <p class="text-white font-bold text-lg">${{ number_format($invoice->total, 2) }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-ocean-400 py-8">
                                <i class="fas fa-file-invoice text-3xl mb-2"></i>
                                <p>No hay facturas hoy</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Facturas del Mes -->
                <div class="card-ocean">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-ocean-400"></i>
                                Facturas del Mes
                            </h3>
                            <div class="flex gap-2 mt-2">
                                <select wire:model.live="selectedMonth" class="input-ocean text-sm">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">{{ Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}</option>
                                    @endfor
                                </select>
                                <select wire:model.live="selectedYear" class="input-ocean text-sm">
                                    @for($year = 2023; $year <= date('Y'); $year++)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-ocean-300">Ingresos del Mes</p>
                            <p class="text-2xl font-bold text-green-400">${{ number_format($monthlyRevenue, 2) }}</p>
                        </div>
                    </div>

                    <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                        @forelse($monthlyInvoices as $invoice)
                            <div class="bg-ocean-800/50 rounded-lg p-4 hover:bg-ocean-800 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-mono text-ocean-300 text-sm">#{{ $invoice->invoice_number }}</span>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $invoice->status === 'paid' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                        {{ $invoice->status === 'paid' ? 'Pagada' : 'Cancelada' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-white font-semibold">Mesa {{ $invoice->order->table->number ?? 'N/A' }}</p>
                                        <p class="text-ocean-400 text-xs">{{ $invoice->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <p class="text-white font-bold text-lg">${{ number_format($invoice->total, 2) }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-ocean-400 py-8">
                                <i class="fas fa-file-invoice text-3xl mb-2"></i>
                                <p>No hay facturas este mes</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'services')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Productos Activos -->
                <div class="card-ocean">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-box text-3xl text-green-400"></i>
                        </div>
                        <div>
                            <p class="text-ocean-300 text-sm">Productos Activos</p>
                            <p class="text-3xl font-bold text-white">{{ $servicesData['active_products'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Categorías -->
                <div class="card-ocean">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-tags text-3xl text-purple-400"></i>
                        </div>
                        <div>
                            <p class="text-ocean-300 text-sm">Total Categorías</p>
                            <p class="text-3xl font-bold text-white">{{ $servicesData['total_categories'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Mesas Totales -->
                <div class="card-ocean">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-chair text-3xl text-blue-400"></i>
                        </div>
                        <div>
                            <p class="text-ocean-300 text-sm">Total Mesas</p>
                            <p class="text-3xl font-bold text-white">{{ $servicesData['total_tables'] }}</p>
                            <p class="text-ocean-400 text-xs mt-1">
                                {{ $servicesData['available_tables'] }} disponibles
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos Más Populares -->
            <div class="card-ocean">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-star text-yellow-400"></i>
                    Productos Más Populares (Histórico)
                </h3>

                <div class="space-y-4">
                    @foreach($servicesData['popular_products'] as $product)
                        <div class="flex items-center justify-between p-4 bg-ocean-800/50 rounded-lg hover:bg-ocean-800 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-ocean-700 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-utensils text-2xl text-ocean-400"></i>
                                </div>
                                <div>
                                    <p class="text-white font-semibold">{{ $product->name }}</p>
                                    <p class="text-ocean-400 text-sm">Total vendido: {{ $product->total_sold }} unidades</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-trophy text-yellow-400"></i>
                                <span class="text-white font-bold">{{ $product->total_sold }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Estilos adicionales -->
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(6, 182, 212, 0.1);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(6, 182, 212, 0.5);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(6, 182, 212, 0.7);
        }
    </style>

    <!-- Script para imprimir -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('print-report', () => {
                window.print();
            });
        });
    </script>
</div>
