<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Administrador',
            'Cocinero',
            'Gerente',
            'Mesero',
            'Contador',
            'AuxiliarGerente',
        ];
        foreach ($roles as $rol) {
            Role::firstOrCreate(['nombre' => $rol]);
        }
    }
}
