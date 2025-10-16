<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Obtener roles del usuario
        $roles = ($user && $user->roles) ? $user->roles->pluck('nombre')->toArray() : ['Administrador'];

        // Debug: Agregar log para verificar los roles
        Log::info('Dashboard roles:', ['roles' => $roles, 'user' => $user->id ?? 'no user']);

        return view('dashboard', compact('user', 'roles'));
    }
}
