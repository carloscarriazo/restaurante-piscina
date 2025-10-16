<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin user for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::where('email', 'admin@bluelagoon.com')->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Admin BluelagOOn',
                'email' => 'admin@bluelagoon.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);
            $this->info('Usuario admin creado: admin@bluelagoon.com / password123');
            $this->info('Usuario ID: ' . $user->id);
        } else {
            $this->info('Usuario admin ya existe: admin@bluelagoon.com');
        }

        return 0;
    }
}
