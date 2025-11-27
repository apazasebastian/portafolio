<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Aquí llamas a tus seeders
        $this->call([
            UserSeeder::class,
            EventoSeeder::class,
        ]);
    }
}