@extends('layouts.app')

@section('title', 'Panel Administrativo - Reservas Deportivas')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Panel Administrativo</h1>
        <p class="text-gray-600">Gestión de reservas de recintos deportivos</p>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100">
                    <div class="w-6 h-6 bg-yellow-600 rounded"></div>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $reservasPendientes }}</h3>
                    <p class="text-gray-600">Reservas Pendientes</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <div class="w-6 h-6 bg-blue-600 rounded"></div>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $reservasHoy }}</h3>
                    <p class="text-gray-600">Reservas Hoy</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <div class="w-6 h-6 bg-green-600 rounded"></div>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $reservasEstesMes }}</h3>
                    <p class="text-gray-600">Este Mes</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <div class="w-6 h-6 bg-purple-600 rounded"></div>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $recintosActivos }}</h3>
                    <p class="text-gray-600">Recintos Activos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservas Pendientes -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Reservas Pendientes</h2>
                @if(isset($reservasPendientesRecientes) && $reservasPendientesRecientes->count() > 0)
                    <a href="{{ route('admin.reservas.index') }}" class="text-blue-600 hover:text-blue-800">
                        Ver todas
                    </a>
                @endif
            </div>
        </div>
        
        <div class="overflow-x-auto">
            @if(isset($reservasPendientesRecientes) && $reservasPendientesRecientes->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Organización</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recinto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha/Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Personas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reservasPendientesRecientes as $reserva)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $reserva->nombre_organizacion }}</div>
                                <div class="text-sm text-gray-500">{{ $reserva->representante_nombre }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $reserva->recinto->nombre }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $reserva->fecha_reserva->format('d/m/Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reserva->cantidad_personas }}</td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <form method="POST" action="{{ route('admin.reservas.aprobar', $reserva) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('¿Aprobar esta reserva?')"
                                                class="text-green-600 hover:text-green-800 text-sm font-medium">
                                            Aprobar
                                        </button>
                                    </form>
                                    <span class="text-gray-300">|</span>
                                    <form method="POST" action="{{ route('admin.reservas.rechazar', $reserva) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="motivo_rechazo" value="Rechazado desde dashboard">
                                        <button type="submit" 
                                                onclick="return confirm('¿Rechazar esta reserva?')"
                                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Rechazar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="px-6 py-8 text-center text-gray-500">
                    <p class="text-lg">No hay reservas pendientes</p>
                    <p class="text-sm mt-1">Todas las reservas han sido procesadas</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Enlaces útiles -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
        <a href="{{ route('calendario') }}" class="bg-blue-100 hover:bg-blue-200 p-6 rounded-lg text-center transition-colors">
            <h3 class="font-semibold text-blue-800">Ver Calendario Público</h3>
            <p class="text-blue-600 text-sm mt-1">Ver disponibilidad de recintos</p>
        </a>
        
        <a href="{{ route('admin.reservas.index') }}" class="bg-green-100 hover:bg-green-200 p-6 rounded-lg text-center transition-colors">
            <h3 class="font-semibold text-green-800">Gestionar Reservas</h3>
            <p class="text-green-600 text-sm mt-1">Ver todas las reservas</p>
        </a>
        
        <a href="{{ route('admin.estadisticas.index') }}" class="bg-purple-100 hover:bg-purple-200 p-6 rounded-lg text-center transition-colors">
            <h3 class="font-semibold text-purple-800">Ver Estadísticas</h3>
            <p class="text-purple-600 text-sm mt-1">Reportes y análisis de uso</p>
        </a>
        
        <div class="bg-gray-100 p-6 rounded-lg text-center">
            <h3 class="font-semibold text-gray-800">Usuario: {{ auth()->user()->name }}</h3>
            <p class="text-gray-600 text-sm mt-1">{{ auth()->user()->email }}</p>
        </div>
    </div>
</div>
@endsection