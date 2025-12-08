<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin
        User::updateOrCreate(
            ['email' => 'admin@hostoo.my.id'],
            [
                'name' => 'Admin Hostoo',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Create Regular User
        User::updateOrCreate(
            ['email' => 'user@hostoo.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );
    }
}
