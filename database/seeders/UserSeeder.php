<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Jefe de Recintos
        User::firstOrCreate(
            ['email' => 'silverwithlotus@gmail.com'],
            [
                'name' => 'Jefe de Recintos',
                'password' => Hash::make('password'),
                'role' => 'jefe_recintos',
                'email_verified_at' => now(),
                'recinto_asignado_id' => null,
                'activo' => true,
            ]
        );

        // Encargado Epicentro 1
        User::firstOrCreate(
            ['email' => 'carlosapazac33@gmail.com'],
            [
                'name' => 'Encargado Epicentro 1',
                'password' => Hash::make('password'),
                'role' => 'encargado_recinto',
                'recinto_asignado_id' => 1,
                'activo' => true,
            ]
        );

        // Encargado Epicentro 2
        User::firstOrCreate(
            ['email' => 'gomezchurabrayan@gmail.com'],
            [
                'name' => 'Encargado Epicentro 2',
                'password' => Hash::make('password'),
                'role' => 'encargado_recinto',
                'recinto_asignado_id' => 2,
                'activo' => true,
            ]
        );

        // Encargado Fortín Sotomayor
        User::firstOrCreate(
            ['email' => 'encargado.fortin@municipalidadarica.cl'],
            [
                'name' => 'Encargado Fortín Sotomayor',
                'password' => Hash::make('password'),
                'role' => 'encargado_recinto',
                'recinto_asignado_id' => 3,
                'activo' => true,
            ]
        );

        // Encargado Piscina Olímpica
        User::firstOrCreate(
            ['email' => 'apazasebastian@gmail.com'],
            [
                'name' => 'Encargado Piscina Olímpica',
                'password' => Hash::make('password'),
                'role' => 'encargado_recinto',
                'recinto_asignado_id' => 4,
                'activo' => true,
            ]
        );
    }
}