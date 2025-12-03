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
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $reservasPendientes }}</h3>
                    <p class="text-gray-600 text-sm">Reservas Pendientes</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $reservasHoy }}</h3>
                    <p class="text-gray-600 text-sm">Reservas Hoy</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $reservasEstesMes }}</h3>
                    <p class="text-gray-600 text-sm">Este Mes</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $recintosActivos }}</h3>
                    <p class="text-gray-600 text-sm">Recintos Activos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Reservas Completa -->
    <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">Gestión de Reservas</h2>
                    <p class="text-sm text-gray-600 mt-1">Visualiza y administra todas las reservas del sistema</p>
                </div>
            </div>
        </div>

        <!-- Sistema de Pestañas/Filtros Rápidos -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.dashboard') }}?filtro=todas" 
                   class="px-4 py-2 rounded-lg font-medium transition-colors border {{ request('filtro', 'todas') == 'todas' ? 'bg-blue-600 text-white border-blue-700' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300' }}">
                    Todas <span class="text-xs ml-1">({{ $todasReservas }})</span>
                </a>
                <a href="{{ route('admin.dashboard') }}?filtro=pendientes" 
                   class="px-4 py-2 rounded-lg font-medium transition-colors border {{ request('filtro') == 'pendientes' ? 'bg-yellow-500 text-white border-yellow-600' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300' }}">
                    Pendientes <span class="text-xs ml-1">({{ $reservasPendientes }})</span>
                </a>
                <a href="{{ route('admin.dashboard') }}?filtro=aprobadas" 
                   class="px-4 py-2 rounded-lg font-medium transition-colors border {{ request('filtro') == 'aprobadas' ? 'bg-green-600 text-white border-green-700' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300' }}">
                    Aprobadas <span class="text-xs ml-1">({{ $contadorAprobadas }})</span>
                </a>
                <a href="{{ route('admin.dashboard') }}?filtro=rechazadas" 
                   class="px-4 py-2 rounded-lg font-medium transition-colors border {{ request('filtro') == 'rechazadas' ? 'bg-red-600 text-white border-red-700' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300' }}">
                    Rechazadas <span class="text-xs ml-1">({{ $contadorRechazadas }})</span>
                </a>
                <a href="{{ route('admin.dashboard') }}?filtro=canceladas" 
                   class="px-4 py-2 rounded-lg font-medium transition-colors border {{ request('filtro') == 'canceladas' ? 'bg-gray-600 text-white border-gray-700' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300' }}">
                    Canceladas <span class="text-xs ml-1">({{ $contadorCanceladas }})</span>
                </a>
            </div>
        </div>

        <!-- Filtros Avanzados -->
        <div class="px-6 py-4 bg-white border-b border-gray-200">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="hidden" name="filtro" value="{{ request('filtro', 'todas') }}">
                
                <div>
                    <label for="recinto_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Recinto
                    </label>
                    <select name="recinto_id" id="recinto_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todos los recintos</option>
                        @foreach($recintos as $recinto)
                            <option value="{{ $recinto->id }}" {{ request('recinto_id') == $recinto->id ? 'selected' : '' }}>
                                {{ $recinto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="deporte" class="block text-sm font-medium text-gray-700 mb-1">
                        Deporte
                    </label>
                    <select name="deporte" id="deporte" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todos los deportes</option>
                        <option value="Fútbol" {{ request('deporte') == 'Fútbol' ? 'selected' : '' }}>Fútbol</option>
                        <option value="Básquetbol" {{ request('deporte') == 'Básquetbol' ? 'selected' : '' }}>Básquetbol</option>
                        <option value="Vóleibol" {{ request('deporte') == 'Vóleibol' ? 'selected' : '' }}>Vóleibol</option>
                        <option value="Tenis" {{ request('deporte') == 'Tenis' ? 'selected' : '' }}>Tenis</option>
                        <option value="Otro" {{ request('deporte') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha
                    </label>
                    <input type="date" name="fecha" id="fecha" value="{{ request('fecha') }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition font-medium">
                        Filtrar
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition text-center font-medium">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabla de Reservas -->
        <div class="overflow-x-auto">
            @if($reservas->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Organización</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Recinto</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Fecha y Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Deporte</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Personas</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reservas as $reserva)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-bold text-gray-900">#{{ $reserva->id }}</span>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-gray-500 to-gray-700 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">
                                            {{ strtoupper(substr($reserva->nombre_organizacion ?? $reserva->representante_nombre, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $reserva->nombre_organizacion ?? 'Sin organización' }}</div>
                                        <div class="text-xs text-gray-500">{{ $reserva->representante_nombre }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $reserva->recinto->nombre ?? 'N/A' }}</div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">{{ $reserva->fecha_reserva->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-600">
                                        {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-300">
                                    {{ $reserva->deporte }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-sm text-gray-900 text-center">
                                <span class="font-semibold">{{ $reserva->cantidad_personas }}</span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $estadoConfig = [
                                        'pendiente' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-400'],
                                        'aprobada' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-400'],
                                        'rechazada' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-400']
                                    ];
                                    $config = $estadoConfig[$reserva->estado] ?? $estadoConfig['pendiente'];
                                @endphp
                                
                                <div class="space-y-1">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }}">
                                        {{ ucfirst($reserva->estado) }}
                                    </span>
                                    
                                    @if($reserva->fecha_cancelacion)
                                        <div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-400">
                                                Cancelada
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2 flex-wrap">
                                    <a href="{{ route('admin.reservas.show', $reserva) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors"
                                       title="Ver detalles">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver
                                    </a>

                                    @if($reserva->puedeReportarIncidencia() && !$reserva->fecha_cancelacion)
                                        <a href="{{ route('admin.incidencias.crear', $reserva->id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-md transition-colors"
                                           title="Reportar incidencia">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            Incidencia
                                            @if($reserva->tieneIncidencias())
                                                <span class="ml-1 bg-white text-orange-600 rounded-full px-1.5 text-xs font-bold">
                                                    {{ $reserva->cantidadIncidencias() }}
                                                </span>
                                            @endif
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Paginación -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Mostrando <span class="font-semibold text-gray-900">{{ $reservas->firstItem() ?? 0 }}</span> 
                            a <span class="font-semibold text-gray-900">{{ $reservas->lastItem() ?? 0 }}</span> 
                            de <span class="font-semibold text-gray-900">{{ $reservas->total() }}</span> reservas
                        </div>
                        
                        @if($reservas->hasPages())
                            {{ $reservas->appends(request()->query())->links() }}
                        @endif
                    </div>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-lg font-medium text-gray-500 mt-4">No hay reservas que coincidan con los filtros</p>
                    <p class="text-sm text-gray-400 mt-1">Intenta cambiar los criterios de búsqueda</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Enlaces útiles -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <a href="{{ route('calendario') }}" class="bg-white hover:bg-gray-50 p-6 rounded-lg text-center transition-all shadow-md hover:shadow-lg border border-gray-200">
            <svg class="w-12 h-12 mx-auto mb-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="font-semibold text-gray-800">Calendario Público</h3>
            <p class="text-gray-600 text-sm mt-1">Ver disponibilidad</p>
        </a>
        
        <a href="{{ route('admin.recintos.index') }}" class="bg-white hover:bg-gray-50 p-6 rounded-lg text-center transition-all shadow-md hover:shadow-lg border border-gray-200">
            <svg class="w-12 h-12 mx-auto mb-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <h3 class="font-semibold text-gray-800">Gestión de Recintos</h3>
            <p class="text-gray-600 text-sm mt-1">Administrar instalaciones</p>
        </a>
        
        <a href="{{ route('admin.estadisticas.index') }}" class="bg-white hover:bg-gray-50 p-6 rounded-lg text-center transition-all shadow-md hover:shadow-lg border border-gray-200">
            <svg class="w-12 h-12 mx-auto mb-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <h3 class="font-semibold text-gray-800">Estadísticas</h3>
            <p class="text-gray-600 text-sm mt-1">Reportes y análisis</p>
        </a>
        
        <div class="bg-white p-6 rounded-lg text-center shadow-md border border-gray-200">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <h3 class="font-semibold text-gray-800">{{ auth()->user()->name }}</h3>
            <p class="text-gray-600 text-sm mt-1">{{ auth()->user()->email }}</p>
            <form action="{{ route('logout') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
</div>
@endsection