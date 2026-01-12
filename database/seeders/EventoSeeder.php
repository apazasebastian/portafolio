<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Evento;
use Carbon\Carbon;

class EventoSeeder extends Seeder
{
    public function run(): void
    {
        $eventos = [
            [
                'titulo' => 'Torneo de Fútbol Juvenil 2025',
                'descripcion' => 'Gran torneo de fútbol para categorías sub-18. Inscripciones abiertas hasta el 20 de noviembre.',
                'fecha_evento' => Carbon::now()->addDays(10),
                'imagen_url' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800',
                'activo' => true,
                'enlace_externo' => null
            ],
            [
                'titulo' => 'Clases de Natación Gratuitas',
                'descripcion' => 'Clases de natación para todas las edades en la Piscina Olímpica. Cupos limitados.',
                'fecha_evento' => Carbon::now()->addDays(15),
                'imagen_url' => 'https://images.unsplash.com/photo-1519315901367-f34ff9154487?w=800',
                'activo' => true,
                'enlace_externo' => null
            ],
            [
                'titulo' => 'Campeonato de Básquetbol',
                'descripcion' => 'Campeonato inter-barrios de básquetbol. Participan 16 equipos de toda la ciudad.',
                'fecha_evento' => Carbon::now()->addDays(20),
                'imagen_url' => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=800',
                'activo' => true,
                'enlace_externo' => null
            ],
            [
                'titulo' => 'Zumba al Aire Libre',
                'descripcion' => 'Sesiones de zumba gratuitas todos los sábados en el Epicentro 2. ¡Trae tu botella de agua!',
                'fecha_evento' => Carbon::now()->addDays(5),
                'imagen_url' => 'https://images.unsplash.com/photo-1518611012118-696072aa579a?w=800',
                'activo' => true,
                'enlace_externo' => null
            ],
            [
                'titulo' => 'Maratón Arica 2025',
                'descripcion' => 'Participa en la carrera más importante del año. 5K, 10K y 21K disponibles.',
                'fecha_evento' => Carbon::now()->addDays(30),
                'imagen_url' => 'https://images.unsplash.com/photo-1452626038306-9aae5e071dd3?w=800',
                'activo' => true,
                'enlace_externo' => null
            ]
        ];

        foreach ($eventos as $evento) {
            // Cambiamos create() por firstOrCreate()
            // Busca por título (único para cada evento)
            Evento::firstOrCreate(
                ['titulo' => $evento['titulo']], // Busca por título
                $evento // Si no existe, crea con todos estos datos
            );
        }

        $this->command->info('Eventos creados exitosamente');
    }
}