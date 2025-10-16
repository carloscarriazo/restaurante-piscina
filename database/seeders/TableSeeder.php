<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Table;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tables = [
            ['name' => 'Mesa 1', 'capacity' => 2, 'status' => 'available'],
            ['name' => 'Mesa 2', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Mesa 3', 'capacity' => 6, 'status' => 'available'],
            ['name' => 'Mesa 4', 'capacity' => 2, 'status' => 'available'],
            ['name' => 'Mesa 5', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Mesa 6', 'capacity' => 8, 'status' => 'available'],
            ['name' => 'Mesa VIP 1', 'capacity' => 6, 'status' => 'available'],
            ['name' => 'Mesa VIP 2', 'capacity' => 8, 'status' => 'available'],
            ['name' => 'Mesa Terraza 1', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Mesa Terraza 2', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Mesa Bar 1', 'capacity' => 2, 'status' => 'available'],
            ['name' => 'Mesa Bar 2', 'capacity' => 2, 'status' => 'available'],
        ];

        foreach ($tables as $table) {
            Table::create($table);
        }
    }
}
