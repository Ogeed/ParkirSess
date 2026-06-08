<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin SmartPark',
            'email' => 'admin@easypark.dev',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->command->info('Admin user created: admin@easypark.dev / password');
    }
}
