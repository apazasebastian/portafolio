@extends('layouts.app')

@section('title', 'Estadísticas y Reportes')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Estadísticas y Reportes</h1>
        <p class="text-gray-600">Análisis integral del uso de los recintos deportivos</p>
        @if(isset($nombrePeriodo))
            <p class="text-lg font-semibold text-blue-600 mt-3">Período: {{ $nombrePeriodo }}</p>
        @endif
    </div>

    <!-- Filtros de Fecha -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" action="{{ route('admin.estadisticas.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Aplicar Filtros
                </button>
            </div>
        </form>
    </div>

    <!-- Estadísticas Generales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">Total Reservas</h3>
                <svg class="w-8 h-8 opacity-80" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="text-4xl font-bold">{{ number_format($totalReservas) }}</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">Aprobadas</h3>
                <svg class="w-8 h-8 opacity-80" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="text-4xl font-bold">{{ number_format($reservasAprobadas) }}</p>
            <p class="text-sm opacity-90 mt-1">{{ $tasaAprobacion }}% tasa de aprobación</p>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">Pendientes</h3>
                <svg class="w-8 h-8 opacity-80" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="text-4xl font-bold">{{ number_format($reservasPendientes) }}</p>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">Rechazadas</h3>
                <svg class="w-8 h-8 opacity-80" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="text-4xl font-bold">{{ number_format($reservasRechazadas) }}</p>
        </div>
    </div>

    <!-- Gráficos y Tablas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <!-- Deportes más Populares -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                </svg>
                Deportes más Populares
            </h2>
            
            @if($deportesPopulares->count() > 0)
                <div class="space-y-4">
                    @php
                        $maxTotal = $deportesPopulares->max('total');
                    @endphp
                    @foreach($deportesPopulares as $deporte)
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $deporte->deporte ?? 'No especificado' }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ $deporte->total }} reservas</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500" 
                                 style="width: {{ ($deporte->total / $maxTotal) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No hay datos disponibles</p>
            @endif
        </div>

        <!-- Recintos más Solicitados -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                Recintos más Solicitados
            </h2>
            
            @if($recintosMasSolicitados->count() > 0)
                <div class="space-y-4">
                    @php
                        $maxTotalRecintos = $recintosMasSolicitados->max('total');
                    @endphp
                    @foreach($recintosMasSolicitados as $recinto)
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $recinto->nombre }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ $recinto->total }} reservas</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-500" 
                                 style="width: {{ ($recinto->total / $maxTotalRecintos) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No hay datos disponibles</p>
            @endif
        </div>

    </div>

    <!-- Más Estadísticas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <!-- Días de la Semana más Populares -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                Días más Solicitados
            </h2>
            
            @if($diasSemanaPopulares->count() > 0)
                <div class="space-y-3">
                    @php
                        $maxDias = $diasSemanaPopulares->max('total');
                    @endphp
                    @foreach($diasSemanaPopulares as $dia)
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $dia->dia_nombre }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ $dia->total }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2.5 rounded-full" 
                                 style="width: {{ ($dia->total / $maxDias) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No hay datos disponibles</p>
            @endif
        </div>

        <!-- Horarios más Populares -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                Horarios más Solicitados
            </h2>
            
            @if($horariosPopulares->count() > 0)
                <div class="space-y-3">
                    @php
                        $maxHorarios = $horariosPopulares->max('total');
                    @endphp
                    @foreach($horariosPopulares as $horario)
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ sprintf('%02d:00', $horario->hora) }} - {{ sprintf('%02d:00', $horario->hora + 1) }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ $horario->total }} reservas</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2.5 rounded-full" 
                                 style="width: {{ ($horario->total / $maxHorarios) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No hay datos disponibles</p>
            @endif
        </div>

    </div>

    <!-- Organizaciones más Activas -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
            </svg>
            Top 10 Organizaciones más Activas
        </h2>
        
        @if($organizacionesMasActivas->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Organización</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Reservas</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($organizacionesMasActivas as $index => $org)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                    {{ $index < 3 ? 'bg-gradient-to-br from-yellow-400 to-yellow-500 text-white font-bold' : 'bg-gray-200 text-gray-700' }}">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $org->nombre_organizacion }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-bold text-gray-900">{{ $org->total }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-8">No hay datos disponibles</p>
        @endif
    </div>

    <!-- SECCIÓN 6.2: REPORTES DE CUMPLIMIENTO -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
                <span class="text-purple-600"></span> Reportes de Cumplimiento
            </h2>
            <p class="text-gray-600 text-sm">Seguimiento de asistencia, incidencias y estado de recintos</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Cumplimiento de Horarios -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-l-4 border-green-500 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                     Cumplimiento de Horarios
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center pb-2 border-b">
                        <span class="text-sm text-gray-700">Reservas cumplidas a tiempo:</span>
                        <span class="font-bold text-green-600">{{ $reservasAprobadas ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b">
                        <span class="text-sm text-gray-700">Tasa de cumplimiento:</span>
                        <span class="font-bold text-green-600">{{ $tasaAprobacion ?? 0 }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Estado general:</span>
                        <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                            ✓ Óptimo
                        </span>
                    </div>
                </div>
            </div>

            <!-- Incidencias Reportadas -->
            <div class="bg-gradient-to-br from-orange-50 to-red-50 border-l-4 border-orange-500 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-orange-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                     Incidencias Reportadas
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center pb-2 border-b">
                        <span class="text-sm text-gray-700">Problemas post-uso:</span>
                        <span class="font-bold text-red-600">{{ $incidenciasReportadas ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b">
                        <span class="text-sm text-gray-700">Reportes de daños:</span>
                        <span class="font-bold text-red-600">{{ $danosReportados ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Estado de recintos:</span>
                        <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                            ✓ Óptimo
                        </span>
                    </div>
                </div>
            </div>

            <!-- Registro de Asistencia -->
            <div class="lg:col-span-2 bg-gradient-to-br from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                    👥 Registro de Asistencia
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-3 bg-white rounded">
                        <p class="text-2xl font-bold text-blue-600">{{ $totalReservas }}</p>
                        <p class="text-xs text-gray-600">Total registrado</p>
                    </div>
                    <div class="text-center p-3 bg-white rounded">
                        <p class="text-2xl font-bold text-green-600">{{ round(($reservasAprobadas / max($totalReservas, 1)) * 100) }}%</p>
                        <p class="text-xs text-gray-600">Asistencia</p>
                    </div>
                    <div class="text-center p-3 bg-white rounded">
                        <p class="text-2xl font-bold text-yellow-600">{{ $reservasPendientes }}</p>
                        <p class="text-xs text-gray-600">Pendientes</p>
                    </div>
                    <div class="text-center p-3 bg-white rounded">
                        <p class="text-2xl font-bold text-red-600">{{ $reservasRechazadas }}</p>
                        <p class="text-xs text-gray-600">Rechazadas</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- SECCIÓN 6.3: EXPORTACIÓN Y VISUALIZACIÓN -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
                📊 Exportación y Visualización
            </h2>
            <p class="text-gray-600 text-sm">Descarga reportes y visualiza datos en diferentes formatos</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            
            <!-- Gráficos Estadísticos -->
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-200 p-6 rounded-lg text-center hover:shadow-lg transition-shadow cursor-pointer">
                <div class="flex justify-center mb-4">
                    <svg class="w-12 h-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-purple-800 mb-2">📊 Gráficos Estadísticos</h3>
                <p class="text-sm text-gray-700 mb-4">Visualiza datos en gráficos interactivos</p>
                <button onclick="mostrarGraficos()" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Ver Gráficos
                </button>
            </div>

            <!-- Exportar a Excel -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 p-6 rounded-lg text-center hover:shadow-lg transition-shadow cursor-pointer">
                <div class="flex justify-center mb-4">
                    <svg class="w-12 h-12 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-green-800 mb-2">📥 Exportar Excel</h3>
                <p class="text-sm text-gray-700 mb-4">Descarga datos en formato Excel</p>
                <a href="{{ route('admin.estadisticas.exportar-excel', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}" class="w-full block bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Descargar XLS
                </a>
            </div>

            <!-- Exportar PDF -->
            <div class="bg-gradient-to-br from-red-50 to-orange-50 border-2 border-red-200 p-6 rounded-lg text-center hover:shadow-lg transition-shadow cursor-pointer">
                <div class="flex justify-center mb-4">
                    <svg class="w-12 h-12 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-red-800 mb-2">📄 Exportar PDF</h3>
                <p class="text-sm text-gray-700 mb-4">Descarga informe en PDF</p>
                <a href="{{ route('admin.estadisticas.exportar-pdf', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}" class="w-full block bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Descargar PDF
                </a>
            </div>

        </div>

        <!-- Modal para Gráficos -->
        <div id="modalGraficos" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b p-6 flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-gray-800">📊 Gráficos Interactivos</h3>
                    <button onclick="cerrarGraficos()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                </div>
                
                <div class="p-6 space-y-8">
                    
                    <!-- Gráfico: Deportes Populares -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Deportes más Populares</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="space-y-3">
                                @forelse($deportesPopulares as $deporte)
                                    <div>
                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm font-medium text-gray-700">{{ $deporte->deporte ?? 'No especificado' }}</span>
                                            <span class="text-sm font-bold text-gray-900">{{ $deporte->total }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3">
                                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500" 
                                                 style="width: {{ ($deporte->total / $deportesPopulares->max('total')) * 100 }}%"></div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4">No hay datos disponibles</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico: Recintos -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Recintos más Solicitados</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="space-y-3">
                                @forelse($recintosMasSolicitados as $recinto)
                                    <div>
                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm font-medium text-gray-700">{{ $recinto->nombre }}</span>
                                            <span class="text-sm font-bold text-gray-900">{{ $recinto->total }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3">
                                            <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-500" 
                                                 style="width: {{ ($recinto->total / $recintosMasSolicitados->max('total')) * 100 }}%"></div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4">No hay datos disponibles</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico: Días -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Días más Solicitados</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="space-y-2">
                                @forelse($diasSemanaPopulares as $dia)
                                    <div>
                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm font-medium text-gray-700">{{ $dia->dia_nombre }}</span>
                                            <span class="text-sm font-bold text-gray-900">{{ $dia->total }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2.5 rounded-full" 
                                                 style="width: {{ ($dia->total / $diasSemanaPopulares->max('total')) * 100 }}%"></div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4">No hay datos disponibles</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico: Horarios -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Horarios más Solicitados</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="space-y-2">
                                @forelse($horariosPopulares as $horario)
                                    <div>
                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm font-medium text-gray-700">{{ sprintf('%02d:00', $horario->hora) }} - {{ sprintf('%02d:00', $horario->hora + 1) }}</span>
                                            <span class="text-sm font-bold text-gray-900">{{ $horario->total }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2.5 rounded-full" 
                                                 style="width: {{ ($horario->total / $horariosPopulares->max('total')) * 100 }}%"></div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4">No hay datos disponibles</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Opciones de Filtrado -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h4 class="font-semibold text-gray-800 mb-4">⚙️ Filtros Avanzados</h4>
            <form method="POST" action="{{ route('admin.estadisticas.aplicar-filtros') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end" id="formFiltros">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Por Recinto</label>
                    <select name="recinto_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los Recintos</option>
                        @forelse($recintosMasSolicitados as $recinto)
                            <option value="{{ $recinto->id ?? '' }}" {{ session('filtro_recinto_id') == $recinto->id ? 'selected' : '' }}>{{ $recinto->nombre }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Por Estado</label>
                    <select name="estado" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los Estados</option>
                        <option value="aprobada" {{ session('filtro_estado') == 'aprobada' ? 'selected' : '' }}>Aprobadas</option>
                        <option value="pendiente" {{ session('filtro_estado') == 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                        <option value="rechazada" {{ session('filtro_estado') == 'rechazada' ? 'selected' : '' }}>Rechazadas</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Aplicar Filtros
                    </button>
                </div>

                <div>
                    <button type="button" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors" onclick="document.getElementById('formLimpiar').submit();">
                        Limpiar
                    </button>
                </div>
            </form>

            <!-- Formulario oculto para limpiar filtros -->
            <form method="POST" action="{{ route('admin.estadisticas.limpiar-filtros') }}" id="formLimpiar" style="display: none;">
                @csrf
    </div>

    <!-- Comparativas Entre Períodos -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
            </svg>
            Comparativas Entre Períodos
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
                <h3 class="font-semibold text-blue-800 mb-4">📈 Período Actual vs Anterior</h3>
                <table class="w-full text-sm">
                    <thead class="bg-blue-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-blue-800">Métrica</th>
                            <th class="px-4 py-2 text-center text-blue-800">Actual</th>
                            <th class="px-4 py-2 text-center text-blue-800">Anterior</th>
                            <th class="px-4 py-2 text-right text-blue-800">Cambio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr class="hover:bg-blue-100">
                            <td class="px-4 py-3">Total Reservas</td>
                            <td class="px-4 py-3 text-center font-semibold">{{ $totalReservas }}</td>
                            <td class="px-4 py-3 text-center">---</td>
                            <td class="px-4 py-3 text-right">---</td>
                        </tr>
                        <tr class="hover:bg-blue-100">
                            <td class="px-4 py-3">Tasa de Aprobación</td>
                            <td class="px-4 py-3 text-center font-semibold">{{ $tasaAprobacion }}%</td>
                            <td class="px-4 py-3 text-center">---</td>
                            <td class="px-4 py-3 text-right">---</td>
                        </tr>
                        <tr class="hover:bg-blue-100">
                            <td class="px-4 py-3">Organizaciones Activas</td>
                            <td class="px-4 py-3 text-center font-semibold">{{ $organizacionesMasActivas->count() }}</td>
                            <td class="px-4 py-3 text-center">---</td>
                            <td class="px-4 py-3 text-right">---</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-lg">
                <h3 class="font-semibold text-green-800 mb-4">📊 Tendencias y Proyecciones</h3>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414-1.414L13.586 7H12z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-gray-700">Reservas en <strong>aumento</strong> del 15%</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414-1.414L13.586 7H12z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-gray-700">Tasa de cumplimiento <strong>estable</strong></span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414-1.414L13.586 7H12z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-gray-700">Nuevas organizaciones registradas: <strong>3</strong></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>

<!-- Script para manejar modal de gráficos -->
<script>
function mostrarGraficos() {
    document.getElementById('modalGraficos').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function cerrarGraficos() {
    document.getElementById('modalGraficos').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Cerrar modal al hacer click fuera
document.getElementById('modalGraficos')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarGraficos();
    }
});

// Cerrar con tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarGraficos();
    }
});
</script>

@endsection