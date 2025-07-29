<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Aquí puedes definir las unidades que deseas insertar en la tabla 'units'
        \App\Models\Unit::create([
            'nombre' => 'Kilogramo',
            'abreviacion' => 'kg',
        ]);

        \App\Models\Unit::create([
            'nombre' => 'Litro',
            'abreviacion' => 'L',
        ]);

        \App\Models\Unit::create([
            'nombre' => 'Metro',
            'abreviacion' => 'm',
        ]);

        \App\Models\Unit::create([
            'nombre' => 'Unidad',
            'abreviacion' => 'u',
        ]);
    }
}
