<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recinto;

class RecintoSeeder extends Seeder
{
    public function run(): void
    {
        $recintos = [
            [
                'nombre' => 'Epicentro 1',
                'descripcion' => 'Multicancha techada para deportes varios (básquetbol, vóleibol, fútsal)',
                'capacidad_maxima' => 100,
                'horarios_disponibles' => json_encode(['inicio' => '08:00', 'fin' => '23:00']),
                'dias_cerrados' => null,
                'activo' => true,
                'imagen_url' => null
            ],
            [
                'nombre' => 'Epicentro 2',
                'descripcion' => 'Multicancha exterior para deportes varios',
                'capacidad_maxima' => 80,
                'horarios_disponibles' => json_encode(['inicio' => '08:00', 'fin' => '23:00']),
                'dias_cerrados' => null,
                'activo' => true,
                'imagen_url' => null
            ],
            [
                'nombre' => 'Fortín Sotomayor',
                'descripcion' => 'Campo de fútbol con césped sintético',
                'capacidad_maxima' => 200,
                'horarios_disponibles' => json_encode(['inicio' => '08:00', 'fin' => '23:00']),
                'dias_cerrados' => null,
                'activo' => true,
                'imagen_url' => null
            ],
            [
                'nombre' => 'Piscina Olímpica',
                'descripcion' => 'Piscina semi-olímpica (CERRADA TODOS LOS LUNES POR MANTENIMIENTO)',
                'capacidad_maxima' => 50,
                'horarios_disponibles' => json_encode(['inicio' => '08:00', 'fin' => '23:00']),
                'dias_cerrados' => json_encode(['monday']),
                'activo' => true,
                'imagen_url' => null
            ]
        ];

        foreach ($recintos as $recinto) {
            // Cambiamos create() por firstOrCreate()
            // El primer parámetro es lo que busca (el nombre único)
            // El segundo parámetro son los demás datos si necesita crear
            Recinto::firstOrCreate(
                ['nombre' => $recinto['nombre']], // Busca por nombre
                $recinto // Si no existe, crea con todos estos datos
            );
        }

        $this->command->info('Recintos creados exitosamente');
    }
}