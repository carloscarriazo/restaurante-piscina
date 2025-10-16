<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            PaymentMethodSeeder::class,
            OrderStatusSeeder::class,
            ProductTypeSeeder::class,
            UnitSeeder::class,
            CategorySeeder::class,
            DailyDiscountSeeder::class,
        ]);
    }
}
