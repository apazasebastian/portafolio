<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener eventos activos ordenados por fecha
        $eventos = Evento::where('activo', true)
            ->where('fecha_evento', '>=', now())
            ->orderBy('fecha_evento', 'asc')
            ->take(5)
            ->get();
        
        // Obtener el mes actual y el siguiente
        $mesActual = Carbon::now()->startOfMonth();
        $mesSiguiente = Carbon::now()->addMonth()->startOfMonth();
        
        // Generar días del mes actual
        $diasMesActual = $this->generarDiasMes($mesActual);
        
        // Generar días del mes siguiente
        $diasMesSiguiente = $this->generarDiasMes($mesSiguiente);
        
        return view('home.index', compact(
            'eventos',
            'mesActual',
            'mesSiguiente',
            'diasMesActual',
            'diasMesSiguiente'
        ));
    }
    
    private function generarDiasMes($mes)
    {
        $dias = [];
        $primerDia = $mes->copy()->startOfMonth();
        $ultimoDia = $mes->copy()->endOfMonth();
        
        // Obtener el día de la semana del primer día (0=Domingo, 6=Sábado)
        $diaSemanaInicio = $primerDia->dayOfWeek;
        
        // Agregar días vacíos al inicio
        for ($i = 0; $i < $diaSemanaInicio; $i++) {
            $dias[] = null;
        }
        
        // Agregar todos los días del mes
        $diaActual = $primerDia->copy();
        while ($diaActual->lte($ultimoDia)) {
            $dias[] = $diaActual->copy();
            $diaActual->addDay();
        }
        
        return $dias;
    }
}
