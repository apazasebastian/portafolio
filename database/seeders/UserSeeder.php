<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Solo crear si no existen
        User::firstOrCreate(
            ['email' => 'jefe.recintos@municipalidadarica.cl'],
            [
                'name' => 'Jefe de Recintos',
                'password' => Hash::make('password'),
                'role' => 'jefe_recintos',
                'activo' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@arica.cl'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'activo' => true,
            ]
        );
    }
}