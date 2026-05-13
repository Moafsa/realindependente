<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@Nexts.com'],
            [
                'name' => 'Administrador',
                'email' => 'admin@Nexts.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Coach user
        User::updateOrCreate(
            ['email' => 'coach@Nexts.com'],
            [
                'name' => 'Treinador',
                'email' => 'coach@Nexts.com',
                'password' => Hash::make('password'),
                'role' => 'coach',
                'is_active' => true,
            ]
        );
    }
}
