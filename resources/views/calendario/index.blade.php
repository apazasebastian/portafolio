@extends('layouts.app')

@section('title', 'Calendario de Recintos Deportivos - Municipalidad de Arica')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">
            Sistema de Reservas - Recintos Deportivos Arica
        </h1>
        <p class="text-gray-600">
            Consulta la disponibilidad y solicita reservas para nuestros recintos deportivos
        </p>
    </div>

    <!-- Lista de Recintos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($recintos as $recinto)
        @php
            // Manejar horarios_disponibles como string o array
            $horarios = is_array($recinto->horarios_disponibles) 
                ? $recinto->horarios_disponibles 
                : json_decode($recinto->horarios_disponibles, true);
            
            // Manejar dias_cerrados como string o array
            $diasCerrados = is_array($recinto->dias_cerrados) 
                ? $recinto->dias_cerrados 
                : ($recinto->dias_cerrados ? json_decode($recinto->dias_cerrados, true) : null);
        @endphp
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $recinto->nombre }}</h3>
            <p class="text-gray-600 text-sm mb-4">{{ $recinto->descripcion }}</p>
            <div class="text-sm text-gray-500 mb-4">
                <p><strong>Capacidad:</strong> {{ $recinto->capacidad_maxima }} personas</p>
                <p><strong>Horario:</strong> {{ $horarios['inicio'] ?? '08:00' }} - {{ $horarios['fin'] ?? '23:00' }}</p>
                @if($diasCerrados && count($diasCerrados) > 0)
                    <p class="text-red-600"><strong>Cerrado:</strong> Lunes (mantenimiento)</p>
                @endif
            </div>
            <a href="{{ route('reservas.create', $recinto) }}" 
               class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition-colors">
                Solicitar Reserva
            </a>
        </div>
        @endforeach
    </div>

<!-- Calendario Simple -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Disponibilidad Próximos 7 Días</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
        @for($i = 0; $i < 7; $i++)
            @php
                $fecha = now()->addDays($i);
                $fechaString = $fecha->format('Y-m-d');
                $esHoy = $fecha->isToday();
            @endphp
            
            <div class="border rounded-lg p-4 {{ $esHoy ? 'ring-2 ring-blue-500' : '' }}">
                <div class="text-center mb-3">
                    <div class="font-semibold text-gray-800">
                        {{ $fecha->locale('es')->format('D') }}
                        @if($esHoy) <span class="text-blue-600">(Hoy)</span> @endif
                    </div>
                    <div class="text-sm text-gray-600">{{ $fecha->format('d/m') }}</div>
                </div>
                
                @if($recintos->count() > 0)
                    @foreach($recintos as $recinto)
                        @php
                            $tieneReservas = isset($reservas[$recinto->id][$fechaString]);
                            
                            // Manejar dias_cerrados como string o array
                            $diasCerradosCalendario = is_array($recinto->dias_cerrados) 
                                ? $recinto->dias_cerrados 
                                : ($recinto->dias_cerrados ? json_decode($recinto->dias_cerrados, true) : null);
                            
                            // Verificar si está cerrado
                            $esDiaCerrado = false;
                            if ($diasCerradosCalendario && is_array($diasCerradosCalendario)) {
                                $esDiaCerrado = in_array(strtolower($fecha->format('l')), $diasCerradosCalendario);
                            }
                        @endphp
                        
                        <div class="text-xs mb-1 p-1 rounded {{ 
                            $esDiaCerrado ? 'bg-gray-200 text-gray-500' : 
                            ($tieneReservas ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700') 
                        }}">
                            {{ Str::limit($recinto->nombre, 15) }}
                            @if($esDiaCerrado)
                                <span class="block text-xs">(Cerrado)</span>
                            @elseif($tieneReservas)
                                <span class="block text-xs">(Ocupado)</span>
                            @else
                                <span class="block text-xs">(Disponible)</span>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-xs text-gray-500 text-center">
                        Sin recintos
                    </div>
                @endif
            </div>
        @endfor
    </div>
</div>
    
    <!-- Información adicional -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">Información Importante</h3>
        <ul class="text-blue-700 space-y-2">
            <li>• Las reservas deben solicitarse con al menos 24 horas de anticipación</li>
            <li>• La Piscina Olímpica permanece cerrada todos los lunes por mantenimiento</li>
            <li>• Horario de funcionamiento: 08:00 - 23:00 horas</li>
            <li>• Todas las solicitudes requieren aprobación del jefe de recintos</li>
            <li>• Recibirá confirmación por correo electrónico</li>
        </ul>
    </div>
</div>
@endsection