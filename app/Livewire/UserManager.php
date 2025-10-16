<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Role;
use App\Models\SessionLog;
use App\Constants\ValidationRules;
use App\Constants\MessageConstants;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * UserManager Component
 *
 * Gestiona la administración completa de usuarios del sistema.
 * Aplica principios SOLID (SRP, OCP, DRY).
 *
 * @property string $name
 * @property string $email
 * @property string $password
 * @property array $selectedRoles
 */
class UserManager extends Component
{
    use WithPagination;

    // Small helper to delegate authorization checks (keeps this class under method limits)
    private $authorizer;

    // Propiedades del formulario
    public $userId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $passwordConfirmation = '';
    public $selectedRoles = [];
    public $isEditMode = false;
    public $showUserForm = false;
    public $showUserLogs = false;
    public $selectedUser = null;

    // Filtros
    public $search = '';
    public $filterRole = '';

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'name' => 'required|min:2',
        'email' => ValidationRules::REQUIRED_EMAIL_UNIQUE,
        'password' => 'nullable|min:6|confirmed',
        'selectedRoles' => 'array'
    ];

    protected $messages = [
        'name.required' => 'El nombre es obligatorio',
        'name.min' => 'El nombre debe tener al menos 2 caracteres',
        'email.required' => 'El email es obligatorio',
        'email.email' => 'El formato del email no es válido',
        'email.unique' => 'Este email ya está registrado',
        'password.required' => 'La contraseña es obligatoria',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres',
        'password.confirmed' => MessageConstants::PASSWORD_MISMATCH
    ];

    public function mount()
    {
        // Inicializar el authorizer que delegará verificaciones de permisos
        $this->authorizer = new UserAuthorizer();

        // Verificar permisos
        if (!$this->authorizer->userCan('Ver Usuarios')) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterRole, function($query) {
                $query->whereHas('roles', function($roleQuery) {
                    $roleQuery->where('nombre', $this->filterRole);
                });
            })
            ->with('roles')
            ->paginate(10);

        $roles = Role::all();

        return view('livewire.user-manager', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function createUser()
    {
        if (!$this->authorizer->authUserCan('Crear Usuarios')) {
            session()->flash('error', 'No tienes permisos para crear usuarios');
            return;
        }

        $this->resetForm();
        $this->showUserForm = true;
    }

    public function editUser($userId)
    {
        if (!$this->authorizer->authUserCan('Editar Usuarios')) {
            session()->flash('error', 'No tienes permisos para editar usuarios');
            return;
        }

        $user = User::findOrFail($userId);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRoles = $user->roles->pluck('id')->toArray();
        $this->isEditMode = true;
        $this->showUserForm = true;

        // Cambiar reglas de validación para edición
        $this->rules['email'] = 'required|email|unique:users,email,' . $this->userId;
        $this->rules['password'] = 'nullable|min:6|confirmed';
    }

    private function getValidationRules()
    {
        $rules = [
            'name' => 'required|min:2',
            'selectedRoles' => 'array'
        ];

        // Email validation
        if ($this->isEditMode) {
            $rules['email'] = 'required|email|unique:users,email,' . $this->userId;
        } else {
            $rules['email'] = 'required|email|unique:users,email';
        }

        // Password validation - custom validation for confirmation
        if ($this->isEditMode) {
            $rules['password'] = 'nullable|min:6';
        } else {
            $rules['password'] = 'required|min:6';
        }

        return $rules;
    }

    protected function rules()
    {
        return $this->getValidationRules();
    }

    // Custom validation for password confirmation
    public function updated($propertyName)
    {
        if ($propertyName === 'password' || $propertyName === 'passwordConfirmation') {
            if ($this->password && $this->password !== $this->passwordConfirmation) {
                $this->addError('passwordConfirmation', 'Las contraseñas no coinciden');
            } else {
                $this->resetErrorBag('passwordConfirmation');
            }
        }

        $this->validateOnly($propertyName);
    }

    public function saveUser()
    {
        $this->logUserSaveAttempt();

        if (!$this->validatePasswordRequirements()) {
            return;
        }

        $this->validate($this->getValidationRules());

        if (!$this->hasPermissionToSave()) {
            return;
        }

        $user = $this->isEditMode ? $this->updateExistingUser() : $this->createNewUser();
        $this->syncUserRoles($user);

        session()->flash('success', $this->isEditMode ? 'Usuario actualizado correctamente' : 'Usuario creado correctamente');
        $this->resetForm();
    }

    private function logUserSaveAttempt(): void
    {
        logger('SaveUser - Modo edición: ' . ($this->isEditMode ? 'true' : 'false'));
        logger('SaveUser - Contraseña: ' . (empty($this->password) ? 'vacía' : 'llena'));
        logger('SaveUser - Confirmación: ' . (empty($this->passwordConfirmation) ? 'vacía' : 'llena'));
        logger('SaveUser - Roles seleccionados: ' . implode(',', $this->selectedRoles ?? []));
    }

    private function validatePasswordRequirements(): bool
    {
        if (!$this->isEditMode && empty($this->password)) {
            $this->addError('password', 'La contraseña es obligatoria');
            return false;
        }

        if ($this->password && $this->password !== $this->passwordConfirmation) {
            $this->addError('passwordConfirmation', 'Las contraseñas no coinciden');
            return false;
        }

        return true;
    }

    private function hasPermissionToSave(): bool
    {
        if (!$this->authorizer->userCan($this->isEditMode ? 'Editar Usuarios' : 'Crear Usuarios')) {
            session()->flash('error', 'No tienes permisos para realizar esta acción');
            return false;
        }

        return true;
    }

    private function updateExistingUser(): User
    {
        $user = User::findOrFail($this->userId);
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        if ($this->password) {
            $user->update(['password' => Hash::make($this->password)]);
        }

        $this->logUserAction('user_updated', $user, 'users.edit', 'updated');
        return $user;
    }

    private function createNewUser(): User
    {
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $this->logUserAction('user_created', $user, 'users.create', 'created');
        return $user;
    }

    private function logUserAction(string $action, User $user, string $route, string $prefix): void
    {
        SessionLog::logAction(
            Auth::id(),
            $action,
            true,
            'users',
            $route,
            ["{$prefix}_user_id" => $user->id, "{$prefix}_user_name" => $user->name]
        );
    }

    private function syncUserRoles(User $user): void
    {
        if (!$this->authorizer->userCan('Gestionar Roles')) {
            return;
        }

        if (!empty($this->selectedRoles)) {
            $user->roles()->sync($this->selectedRoles);
            $roleNames = Role::whereIn('id', $this->selectedRoles)->pluck('nombre')->implode(', ');
            session()->flash('info', "Roles asignados: {$roleNames}");
        } else {
            $user->roles()->detach();
        }
    }

    public function deleteUser($userId)
    {
        if (!$this->authorizer->userCan('Eliminar Usuarios')) {
            session()->flash('error', 'No tienes permisos para eliminar usuarios');
            return;
        }

        $user = User::findOrFail($userId);

        // No permitir eliminar el propio usuario
        if ($user->id === Auth::id()) {
            session()->flash('error', 'No puedes eliminar tu propio usuario');
            return;
        }

        $userName = $user->name;
        $user->delete();

        SessionLog::logAction(
            Auth::id(),
            'user_deleted',
            true,
            'users',
            'users.delete',
            ['deleted_user_name' => $userName]
        );

        session()->flash('success', 'Usuario eliminado correctamente');
    }

    public function showLogs($userId)
    {
        if (!$this->authorizer->userCan('Ver Logs')) {
            session()->flash('error', 'No tienes permisos para ver logs');
            return;
        }

        $this->selectedUser = User::with('sessionLogs')->findOrFail($userId);
        $this->showUserLogs = true;
    }

    public function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->passwordConfirmation = '';
        $this->selectedRoles = [];
        $this->isEditMode = false;
        $this->showUserForm = false;
        $this->resetErrorBag();

        // Restaurar reglas de validación originales
        $this->rules['email'] = 'required|email|unique:users,email';
        $this->rules['password'] = 'required|min:6|confirmed';
    }

    public function closeModal()
    {
        $this->showUserForm = false;
        $this->showUserLogs = false;
        $this->selectedUser = null;
        $this->resetForm();
    }
}

/**
 * Clase auxiliar responsable de comprobaciones de permisos.
 * Separada para mantener UserManager por debajo del límite de métodos.
 */
class UserAuthorizer
{
    /**
     * Verifica si el usuario autenticado tiene un permiso específico.
     *
     * @param string $permission
     * @return bool
     */
    public function userCan(string $permission): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return method_exists($user, 'hasPermission') ? $user->hasPermission($permission) : false;
    }

    /**
     * Verifica si el usuario autenticado tiene un permiso específico (helper global).
     *
     * @param string $permission
     * @return bool
     */
    public function authUserCan(string $permission): bool
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return false;
        }

        return method_exists($authUser, 'hasPermission') ? $authUser->hasPermission($permission) : false;
    }
}
