<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center bg-gradient-to-r from-blue-600 via-cyan-600 to-teal-600 p-6 rounded-lg shadow-lg">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight">
                    ✏️ {{ __('Editar Mesa') }}
                </h2>
                <p class="text-blue-100 mt-1">Modifica los datos de la mesa {{ $table->number }}</p>
            </div>
            <a href="{{ route('tables.index') }}"
               class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-3 px-6 rounded-full shadow-lg transform transition hover:scale-105 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8">
                    <form action="{{ route('tables.update', $table) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Columna 1 -->
                            <div class="space-y-6">
                                <!-- Número de mesa -->
                                <div>
                                    <label for="number" class="block text-sm font-medium text-gray-700">
                                        Número de Mesa <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="number"
                                           id="number"
                                           value="{{ old('number', $table->number) }}"
                                           class="mt-1 block w-full rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500 {{ $errors->has('number') ? 'border-red-500' : 'border-gray-300' }}"
                                           placeholder="Ej: 01, A1, Mesa-1">
                                    @error('number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Nombre de la mesa -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">
                                        Nombre de la Mesa <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="name"
                                           id="name"
                                           value="{{ old('name', $table->name) }}"
                                           class="mt-1 block w-full rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500 {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}"
                                           placeholder="Ej: Mesa Principal, Mesa Terraza">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Capacidad -->
                                <div>
                                    <label for="capacity" class="block text-sm font-medium text-gray-700">
                                        Capacidad (Personas) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number"
                                           name="capacity"
                                           id="capacity"
                                           min="1"
                                           max="20"
                                           value="{{ old('capacity', $table->capacity) }}"
                                           class="mt-1 block w-full rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500 {{ $errors->has('capacity') ? 'border-red-500' : 'border-gray-300' }}"
                                           placeholder="4">
                                    @error('capacity')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Columna 2 -->
                            <div class="space-y-6">
                                <!-- Ubicación -->
                                <div>
                                    <label for="location" class="block text-sm font-medium text-gray-700">
                                        Ubicación
                                    </label>
                                    <input type="text"
                                           name="location"
                                           id="location"
                                           value="{{ old('location', $table->location) }}"
                                           class="mt-1 block w-full rounded-lg {{ $errors->has('location') ? 'border-red-500' : 'border-gray-300' }} shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                           placeholder="Ej: Terraza, Salón principal, Junto a la ventana">
                                    @error('location')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Estado -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700">
                                        Estado <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status"
                                            id="status"
                                            class="mt-1 block w-full rounded-lg {{ $errors->has('status') ? 'border-red-500' : 'border-gray-300' }} shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                                        <option value="available" {{ old('status', $table->status) == 'available' ? 'selected' : '' }}>
                                            ✓ Disponible
                                        </option>
                                        <option value="occupied" {{ old('status', $table->status) == 'occupied' ? 'selected' : '' }}>
                                            👥 Ocupada
                                        </option>
                                        <option value="reserved" {{ old('status', $table->status) == 'reserved' ? 'selected' : '' }}>
                                            📅 Reservada
                                        </option>
                                        <option value="maintenance" {{ old('status', $table->status) == 'maintenance' ? 'selected' : '' }}>
                                            🔧 Mantenimiento
                                        </option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Disponibilidad -->
                                <div>
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                               name="is_available"
                                               id="is_available"
                                               value="1"
                                               {{ old('is_available', $table->is_available) ? 'checked' : '' }}
                                               class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded">
                                        <label for="is_available" class="ml-2 block text-sm text-gray-900">
                                            Mesa habilitada para uso
                                        </label>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Las mesas deshabilitadas no aparecerán disponibles para nuevas órdenes
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Vista previa del estado -->
                        <div class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg border border-blue-200">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Vista Previa de la Mesa</h4>
                            <div class="flex items-center justify-between bg-white p-4 rounded-lg shadow">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900" id="preview-number">Mesa {{ $table->number }}</h3>
                                    <p class="text-gray-600" id="preview-name">{{ $table->name }}</p>
                                    <p class="text-sm text-gray-500" id="preview-capacity">{{ $table->capacity }} personas</p>
                                    <p class="text-sm text-gray-500" id="preview-location">{{ $table->location ?? 'Ubicación no especificada' }}</p>
                                </div>
                                <div>
                                    <span id="preview-status" class="
                                        @switch($table->status)
                                            @case('available')
                                                bg-green-100 text-green-800
                                                @break
                                            @case('occupied')
                                                bg-red-100 text-red-800
                                                @break
                                            @case('reserved')
                                                bg-yellow-100 text-yellow-800
                                                @break
                                            @case('maintenance')
                                                bg-gray-100 text-gray-800
                                                @break
                                        @endswitch
                                        text-xs font-semibold px-3 py-1 rounded-full">
                                        @switch($table->status)
                                            @case('available')
                                                ✓ Disponible
                                                @break
                                            @case('occupied')
                                                👥 Ocupada
                                                @break
                                            @case('reserved')
                                                📅 Reservada
                                                @break
                                            @case('maintenance')
                                                🔧 Mantenimiento
                                                @break
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end space-x-4">
                            <a href="{{ route('tables.index') }}"
                               class="bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white font-bold py-3 px-6 rounded-full shadow-lg transform transition hover:scale-105 inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-600 hover:to-cyan-600 text-white font-bold py-3 px-6 rounded-full shadow-lg transform transition hover:scale-105 inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Actualizar Mesa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Actualizar vista previa en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const numberInput = document.getElementById('number');
            const nameInput = document.getElementById('name');
            const capacityInput = document.getElementById('capacity');
            const locationInput = document.getElementById('location');
            const statusSelect = document.getElementById('status');

            const previewNumber = document.getElementById('preview-number');
            const previewName = document.getElementById('preview-name');
            const previewCapacity = document.getElementById('preview-capacity');
            const previewLocation = document.getElementById('preview-location');
            const previewStatus = document.getElementById('preview-status');

            function updatePreview() {
                previewNumber.textContent = numberInput.value ? `Mesa ${numberInput.value}` : 'Mesa --';
                previewName.textContent = nameInput.value || 'Nombre de la mesa';
                previewCapacity.textContent = capacityInput.value ? `${capacityInput.value} personas` : '-- personas';
                previewLocation.textContent = locationInput.value || 'Ubicación no especificada';

                // Actualizar estado
                const statusValue = statusSelect.value;
                let statusText = '';
                let statusClass = '';

                switch(statusValue) {
                    case 'available':
                        statusText = '✓ Disponible';
                        statusClass = 'bg-green-100 text-green-800';
                        break;
                    case 'occupied':
                        statusText = '👥 Ocupada';
                        statusClass = 'bg-red-100 text-red-800';
                        break;
                    case 'reserved':
                        statusText = '📅 Reservada';
                        statusClass = 'bg-yellow-100 text-yellow-800';
                        break;
                    case 'maintenance':
                        statusText = '🔧 Mantenimiento';
                        statusClass = 'bg-gray-100 text-gray-800';
                        break;
                }

                previewStatus.textContent = statusText;
                previewStatus.className = `${statusClass} text-xs font-semibold px-3 py-1 rounded-full`;
            }

            // Eventos para actualizar la vista previa
            numberInput.addEventListener('input', updatePreview);
            nameInput.addEventListener('input', updatePreview);
            capacityInput.addEventListener('input', updatePreview);
            locationInput.addEventListener('input', updatePreview);
            statusSelect.addEventListener('change', updatePreview);

            // Actualización inicial
            updatePreview();
        });
    </script>
</x-app-layout>
