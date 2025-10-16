<div class="min-h-screen bg-gradient-to-br from-ocean-950 via-ocean-900 to-blue-950">
    <!-- Header -->
    <div class="bg-gradient-to-r from-ocean-600 via-ocean-500 to-blue-500 shadow-ocean">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                        <i class="fas fa-users"></i>
                        Gestión de Usuarios
                    </h1>
                    <p class="text-ocean-100 mt-1">Administra usuarios, roles y permisos del sistema</p>
                </div>
                @if(auth()->user() && auth()->user()->hasPermission('users.create'))
                <button wire:click="createUser" class="btn-ocean">
                    <i class="fas fa-user-plus mr-2"></i>
                    Nuevo Usuario
                </button>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Mensajes de alerta -->
        @if (session()->has('success'))
            <div class="mb-6 bg-green-500/20 border border-green-500/30 text-green-400 px-6 py-4 rounded-xl backdrop-blur-sm flex items-center gap-3">
                <i class="fas fa-check-circle text-2xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-4 rounded-xl backdrop-blur-sm flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-2xl"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @if (session()->has('info'))
            <div class="mb-6 bg-blue-500/20 border border-blue-500/30 text-blue-400 px-6 py-4 rounded-xl backdrop-blur-sm flex items-center gap-3">
                <i class="fas fa-info-circle text-2xl"></i>
                <span class="font-medium">{{ session('info') }}</span>
            </div>
        @endif

        <!-- Filtros y búsqueda -->
        <div class="card-ocean mb-6">
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex-1 min-w-[200px]">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-ocean-400"></i>
                        <input type="text"
                               wire:model.live="search"
                               placeholder="Buscar por nombre o email..."
                               class="input-ocean w-full pl-10">
                    </div>
                </div>

                <select wire:model.live="filterRole" class="input-ocean">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->nombre }}">{{ $role->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Grid de usuarios -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($users as $user)
                <div class="card-ocean hover-scale">
                    <!-- Header de la card -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                                <i class="fas fa-user-circle text-ocean-400"></i>
                                {{ $user->name }}
                            </h3>
                            <p class="text-ocean-300 text-sm flex items-center gap-1">
                                <i class="fas fa-envelope text-xs"></i>
                                {{ $user->email }}
                            </p>
                        </div>
                    </div>

                    <!-- Roles -->
                    <div class="mb-4">
                        <p class="text-ocean-400 text-xs font-medium mb-2">ROLES</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse($user->roles as $role)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-500/20 text-purple-400 border border-purple-500/30">
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    {{ $role->nombre }}
                                </span>
                            @empty
                                <span class="text-ocean-400 text-sm">Sin roles asignados</span>
                            @endforelse
                        </div>
                    </div>

                    <!-- Fecha de registro -->
                    <div class="bg-ocean-800/50 rounded-lg p-3 mb-4">
                        <p class="text-ocean-400 text-xs mb-1">REGISTRADO</p>
                        <p class="text-white font-medium flex items-center gap-2">
                            <i class="fas fa-calendar text-ocean-400"></i>
                            {{ $user->created_at->format('d/m/Y') }}
                        </p>
                    </div>

                    <!-- Acciones -->
                    <div class="flex gap-2">
                        @if(auth()->user() && auth()->user()->hasPermission('users.edit'))
                        <button wire:click="editUser({{ $user->id }})"
                                class="flex-1 bg-ocean-700 hover:bg-ocean-600 text-white px-3 py-2 rounded-lg font-medium transition-all duration-300 flex items-center justify-center gap-2 text-sm">
                            <i class="fas fa-edit"></i>
                            <span>Editar</span>
                        </button>
                        @endif

                        @if(auth()->user() && auth()->user()->hasPermission('config.logs'))
                        <button wire:click="showLogs({{ $user->id }})"
                                class="bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-400 border border-yellow-500/30 px-3 py-2 rounded-lg font-medium transition-all duration-300 flex items-center justify-center text-sm">
                            <i class="fas fa-clipboard-list"></i>
                        </button>
                        @endif

                        @if(auth()->user() && auth()->user()->hasPermission('users.delete') && $user->id !== auth()->id())
                        <button wire:click="deleteUser({{ $user->id }})"
                                wire:confirm="¿Estás seguro de eliminar este usuario?"
                                class="bg-red-500/20 hover:bg-red-500/30 text-red-400 border border-red-500/30 px-3 py-2 rounded-lg font-medium transition-all duration-300 flex items-center justify-center text-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="card-ocean text-center py-12">
                        <i class="fas fa-users text-6xl text-ocean-600 mb-4"></i>
                        <h3 class="text-xl font-bold text-white mb-2">No hay usuarios</h3>
                        <p class="text-ocean-300 mb-6">No se encontraron usuarios con los filtros aplicados</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        @if($users->hasPages())
        <div class="mt-6">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Formulario Usuario -->
    @if($showUserForm)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
            <div class="relative w-full max-w-2xl">
                <div class="card-ocean">
                    <!-- Header del modal -->
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-{{ $isEditMode ? 'user-edit' : 'user-plus' }} text-ocean-400"></i>
                            {{ $isEditMode ? 'Editar Usuario' : 'Nuevo Usuario' }}
                        </h3>
                        <button wire:click="closeModal" class="text-ocean-400 hover:text-white transition-colors">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>

                    <!-- Formulario -->
                    <form wire:submit.prevent="saveUser">
                        <div class="space-y-6">
                            <!-- Nombre -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-ocean-200 mb-2">
                                    <i class="fas fa-user mr-1"></i>
                                    Nombre Completo <span class="text-red-400">*</span>
                                </label>
                                <input type="text"
                                       id="name"
                                       wire:model="name"
                                       class="input-ocean w-full @error('name') border-red-500 @enderror"
                                       placeholder="Ej: Juan Pérez">
                                @error('name')
                                    <span class="text-red-400 text-sm flex items-center gap-1 mt-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="user-email" class="block text-sm font-medium text-ocean-200 mb-2">
                                    <i class="fas fa-envelope mr-1"></i>
                                    Correo Electrónico <span class="text-red-400">*</span>
                                </label>
                                <input id="user-email"
                                       type="email"
                                       wire:model="email"
                                       class="input-ocean w-full @error('email') border-red-500 @enderror"
                                       placeholder="correo@ejemplo.com">
                                @error('email')
                                    <span class="text-red-400 text-sm flex items-center gap-1 mt-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <!-- Contraseña -->
                            <div>
                                <label for="user-password" class="block text-sm font-medium text-ocean-200 mb-2">
                                    <i class="fas fa-lock mr-1"></i>
                                    Contraseña {{ $isEditMode ? '(dejar vacío para mantener)' : '' }} <span class="text-red-400">{{ $isEditMode ? '' : '*' }}</span>
                                </label>
                                <input id="user-password"
                                       type="password"
                                       wire:model="password"
                                       class="input-ocean w-full @error('password') border-red-500 @enderror"
                                       placeholder="••••••••">
                                @error('password')
                                    <span class="text-red-400 text-sm flex items-center gap-1 mt-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div>
                                <label for="user-password-confirmation" class="block text-sm font-medium text-ocean-200 mb-2">
                                    <i class="fas fa-lock mr-1"></i>
                                    Confirmar Contraseña
                                </label>
                                <input id="user-password-confirmation"
                                       type="password"
                                       wire:model="passwordConfirmation"
                                       class="input-ocean w-full"
                                       placeholder="••••••••">
                            </div>

                            <!-- Roles -->
                            @if(auth()->user() && auth()->user()->hasPermission('users.roles'))
                            <div class="bg-ocean-800/50 rounded-lg p-4">
                                <fieldset>
                                    <legend class="text-ocean-200 font-medium mb-3 flex items-center gap-2">
                                        <i class="fas fa-shield-alt text-ocean-400"></i>
                                        Roles del Usuario
                                    </legend>
                                    <div class="space-y-3">
                                        @foreach($roles as $role)
                                        <label for="role-{{ $role->id }}" class="flex items-center gap-3 cursor-pointer hover:bg-ocean-700/30 p-2 rounded-lg transition-colors">
                                            <input id="role-{{ $role->id }}"
                                                   type="checkbox"
                                                   wire:model="selectedRoles"
                                                   value="{{ $role->id }}"
                                                   class="w-5 h-5 text-ocean-500 bg-ocean-800 border-ocean-600 rounded focus:ring-ocean-500 focus:ring-2">
                                            <span class="text-white font-medium">{{ $role->nombre }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </fieldset>
                            </div>
                            @endif
                        </div>

                        <!-- Botones del modal -->
                        <div class="mt-8 flex justify-end gap-3">
                            <button type="button" wire:click="closeModal"
                                    class="btn-ocean-secondary">
                                <i class="fas fa-times mr-2"></i>
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="btn-ocean">
                                <i class="fas fa-save mr-2"></i>
                                {{ $isEditMode ? 'Actualizar' : 'Crear' }} Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Logs de Usuario -->
    @if($showUserLogs && $selectedUser)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
            <div class="relative w-full max-w-5xl">
                <div class="card-ocean">
                    <!-- Header del modal -->
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-clipboard-list text-ocean-400"></i>
                            Logs de Actividad - {{ $selectedUser->name }}
                        </h3>
                        <button wire:click="closeModal" class="text-ocean-400 hover:text-white transition-colors">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>

                    <!-- Tabla de logs -->
                    <div class="overflow-x-auto max-h-96">
                        <table class="w-full">
                            <thead class="bg-ocean-800 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-ocean-200 uppercase">Fecha</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-ocean-200 uppercase">Acción</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-ocean-200 uppercase">Estado</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-ocean-200 uppercase">Detalles</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-ocean-700">
                                @forelse($selectedUser->sessionLogs()->latest()->limit(50)->get() as $log)
                                <tr class="hover:bg-ocean-800/30 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-ocean-200">
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-white font-medium">
                                        {{ $log->action }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($log->success)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Exitoso
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                Fallido
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-ocean-300">
                                        @if($log->details)
                                            {{ is_array($log->details) ? implode(', ', $log->details) : $log->details }}
                                        @else
                                            <span class="text-ocean-500">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-ocean-400">
                                        <i class="fas fa-inbox text-3xl mb-2"></i>
                                        <p>No hay registros de actividad</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer del modal -->
                    <div class="mt-6 flex justify-end">
                        <button wire:click="closeModal" class="btn-ocean-secondary">
                            <i class="fas fa-times mr-2"></i>
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
