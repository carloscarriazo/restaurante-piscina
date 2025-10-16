<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Principal',
                'email' => 'admin@bluelagoon.com',
                'password' => Hash::make('password'),
                'role' => 'Administrador',
            ],
            [
                'name' => 'Chef Cocinero',
                'email' => 'cocinero@bluelagoon.com',
                'password' => Hash::make('password'),
                'role' => 'Cocinero',
            ],
            [
                'name' => 'Gerente General',
                'email' => 'gerente@bluelagoon.com',
                'password' => Hash::make('password'),
                'role' => 'Gerente',
            ],
            [
                'name' => 'Mesero Uno',
                'email' => 'mesero1@bluelagoon.com',
                'password' => Hash::make('password'),
                'role' => 'Mesero',
            ],
                        [
                'name' => 'Mesero Dos',
                'email' => 'mesero2@bluelagoon.com',
                'password' => Hash::make('password'),
                'role' => 'Mesero',
            ],
                        [
                'name' => 'Mesero Tres',
                'email' => 'mesero3@bluelagoon.com',
                'password' => Hash::make('password'),
                'role' => 'Mesero',
            ],
                        [
                'name' => 'Mesero Cuatro',
                'email' => 'mesero4@bluelagoon.com',
                'password' => Hash::make('password'),
                'role' => 'Mesero',
            ],
                        [
                'name' => 'Mesero Cinco',
                'email' => 'mesero5@bluelagoon.com',
                'password' => Hash::make('password'),
                'role' => 'Mesero',
            ],
                        [
                'name' => 'Mesero Seis',
                'email' => 'mesero6@bluelagoon.com',
                'password' => Hash::make('password'),
                'role' => 'Mesero',
            ],
            [
                'name' => 'Contador Oficial',
                'email' => 'contador@bluelagoon.com',
                'password' => Hash::make('password'),
                'role' => 'Contador',
            ],
            [
                'name' => 'Auxiliar Gerente',
                'email' => 'auxgerente@bluelagoon.com',
                'password' => Hash::make('password'),
                'role' => 'AuxiliarGerente',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate([
                'email' => $data['email']
            ], [
                'name' => $data['name'],
                'password' => $data['password'],
            ]);
            $role = Role::where('nombre', $data['role'])->first();
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }
        }
    }
}
