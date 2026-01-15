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

    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
        <svg class="w-7 h-7 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Comparativa Entre Periodos
    </h2>

    <!-- Selector de periodo de comparación -->
    <form method="GET" action="{{ route('admin.estadisticas.index') }}" class="mb-6">
        <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio', now()->startOfMonth()->format('Y-m-d')) }}">
        <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin', now()->endOfDay()->format('Y-m-d')) }}">
        
        <div class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Comparar con:</label>
                <select name="periodo_comparacion" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        onchange="this.form.submit()">
                    <option value="">Seleccionar periodo...</option>
                    <option value="mes_anterior" {{ request('periodo_comparacion') == 'mes_anterior' ? 'selected' : '' }}>
                        Mes Anterior
                    </option>
                    <option value="trimestre_anterior" {{ request('periodo_comparacion') == 'trimestre_anterior' ? 'selected' : '' }}>
                        Trimestre Anterior
                    </option>
                    <option value="año_anterior" {{ request('periodo_comparacion') == 'año_anterior' ? 'selected' : '' }}>
                        Mismo Periodo Año Anterior
                    </option>
                </select>
            </div>
        </div>
    </form>

    @if(isset($datosComparacion))
        <!-- Resultados de la comparativa -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Periodo Actual -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-300 rounded-lg p-6">
                <h3 class="text-lg font-bold text-blue-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Periodo Actual
                </h3>
                <p class="text-sm text-blue-700 font-semibold mb-4">{{ $datosComparacion['periodo_actual']['nombre'] }}</p>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Total Reservas:</span>
                        <span class="text-2xl font-bold text-blue-900">{{ $datosComparacion['periodo_actual']['datos']['total'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Aprobadas:</span>
                        <span class="text-lg font-semibold text-green-600">{{ $datosComparacion['periodo_actual']['datos']['aprobadas'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Rechazadas:</span>
                        <span class="text-lg font-semibold text-red-600">{{ $datosComparacion['periodo_actual']['datos']['rechazadas'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Pendientes:</span>
                        <span class="text-lg font-semibold text-yellow-600">{{ $datosComparacion['periodo_actual']['datos']['pendientes'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Periodo de Comparación -->
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-gray-300 rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                    Periodo Anterior
                </h3>
                <p class="text-sm text-gray-700 font-semibold mb-4">{{ $datosComparacion['periodo_comparacion']['nombre'] }}</p>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Total Reservas:</span>
                        <span class="text-2xl font-bold text-gray-900">{{ $datosComparacion['periodo_comparacion']['datos']['total'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Aprobadas:</span>
                        <span class="text-lg font-semibold text-green-600">{{ $datosComparacion['periodo_comparacion']['datos']['aprobadas'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Rechazadas:</span>
                        <span class="text-lg font-semibold text-red-600">{{ $datosComparacion['periodo_comparacion']['datos']['rechazadas'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Pendientes:</span>
                        <span class="text-lg font-semibold text-yellow-600">{{ $datosComparacion['periodo_comparacion']['datos']['pendientes'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Diferencias y Porcentajes -->
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-lg p-6">
            <h3 class="text-lg font-bold text-purple-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                Diferencias y Tendencias
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Total -->
                <div class="bg-white rounded-lg p-4 text-center">
                    <p class="text-xs text-gray-600 mb-2">Total</p>
                    <p class="text-2xl font-bold {{ $datosComparacion['diferencias']['total'] > 0 ? 'text-green-600' : ($datosComparacion['diferencias']['total'] < 0 ? 'text-red-600' : 'text-gray-600') }}">
                        {{ $datosComparacion['diferencias']['total'] > 0 ? '+' : '' }}{{ $datosComparacion['diferencias']['total'] }}
                    </p>
                    <p class="text-sm font-semibold {{ $datosComparacion['porcentajes']['total'] > 0 ? 'text-green-600' : ($datosComparacion['porcentajes']['total'] < 0 ? 'text-red-600' : 'text-gray-600') }}">
                        {{ $datosComparacion['porcentajes']['total'] > 0 ? '↑' : ($datosComparacion['porcentajes']['total'] < 0 ? '↓' : '→') }} 
                        {{ abs($datosComparacion['porcentajes']['total']) }}%
                    </p>
                </div>

                <!-- Aprobadas -->
                <div class="bg-white rounded-lg p-4 text-center">
                    <p class="text-xs text-gray-600 mb-2">Aprobadas</p>
                    <p class="text-2xl font-bold {{ $datosComparacion['diferencias']['aprobadas'] > 0 ? 'text-green-600' : ($datosComparacion['diferencias']['aprobadas'] < 0 ? 'text-red-600' : 'text-gray-600') }}">
                        {{ $datosComparacion['diferencias']['aprobadas'] > 0 ? '+' : '' }}{{ $datosComparacion['diferencias']['aprobadas'] }}
                    </p>
                    <p class="text-sm font-semibold {{ $datosComparacion['porcentajes']['aprobadas'] > 0 ? 'text-green-600' : ($datosComparacion['porcentajes']['aprobadas'] < 0 ? 'text-red-600' : 'text-gray-600') }}">
                        {{ $datosComparacion['porcentajes']['aprobadas'] > 0 ? '↑' : ($datosComparacion['porcentajes']['aprobadas'] < 0 ? '↓' : '→') }} 
                        {{ abs($datosComparacion['porcentajes']['aprobadas']) }}%
                    </p>
                </div>

                <!-- Rechazadas -->
                <div class="bg-white rounded-lg p-4 text-center">
                    <p class="text-xs text-gray-600 mb-2">Rechazadas</p>
                    <p class="text-2xl font-bold {{ $datosComparacion['diferencias']['rechazadas'] > 0 ? 'text-red-600' : ($datosComparacion['diferencias']['rechazadas'] < 0 ? 'text-green-600' : 'text-gray-600') }}">
                        {{ $datosComparacion['diferencias']['rechazadas'] > 0 ? '+' : '' }}{{ $datosComparacion['diferencias']['rechazadas'] }}
                    </p>
                    <p class="text-sm font-semibold {{ $datosComparacion['porcentajes']['rechazadas'] > 0 ? 'text-red-600' : ($datosComparacion['porcentajes']['rechazadas'] < 0 ? 'text-green-600' : 'text-gray-600') }}">
                        {{ $datosComparacion['porcentajes']['rechazadas'] > 0 ? '↑' : ($datosComparacion['porcentajes']['rechazadas'] < 0 ? '↓' : '→') }} 
                        {{ abs($datosComparacion['porcentajes']['rechazadas']) }}%
                    </p>
                </div>

                <!-- Pendientes -->
                <div class="bg-white rounded-lg p-4 text-center">
                    <p class="text-xs text-gray-600 mb-2">Pendientes</p>
                    <p class="text-2xl font-bold {{ $datosComparacion['diferencias']['pendientes'] > 0 ? 'text-yellow-600' : ($datosComparacion['diferencias']['pendientes'] < 0 ? 'text-green-600' : 'text-gray-600') }}">
                        {{ $datosComparacion['diferencias']['pendientes'] > 0 ? '+' : '' }}{{ $datosComparacion['diferencias']['pendientes'] }}
                    </p>
                    <p class="text-sm font-semibold {{ $datosComparacion['porcentajes']['pendientes'] > 0 ? 'text-yellow-600' : ($datosComparacion['porcentajes']['pendientes'] < 0 ? 'text-green-600' : 'text-gray-600') }}">
                        {{ $datosComparacion['porcentajes']['pendientes'] > 0 ? '↑' : ($datosComparacion['porcentajes']['pendientes'] < 0 ? '↓' : '→') }} 
                        {{ abs($datosComparacion['porcentajes']['pendientes']) }}%
                    </p>
                </div>
            </div>

            <!-- Interpretación -->
            <div class="mt-4 p-4 bg-white rounded-lg">
                <p class="text-sm text-gray-700">
                    <strong>Análisis:</strong>
                    @if($datosComparacion['diferencias']['total'] > 0)
                        El total de reservas ha <span class="text-green-600 font-semibold">aumentado un {{ abs($datosComparacion['porcentajes']['total']) }}%</span> respecto al periodo anterior.
                    @elseif($datosComparacion['diferencias']['total'] < 0)
                        El total de reservas ha <span class="text-red-600 font-semibold">disminuido un {{ abs($datosComparacion['porcentajes']['total']) }}%</span> respecto al periodo anterior.
                    @else
                        El total de reservas se mantiene <span class="font-semibold">igual</span> al periodo anterior.
                    @endif
                </p>
            </div>
        </div>
    @else
        <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <p class="text-gray-600">Selecciona un periodo para comparar</p>
        </div>
    @endif
</div>


    <!-- Estadísticas Generales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">Total Reservas</h3>
                <svg class="w-8 h-8 opacity-80" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="text-4xl font-bold">{{ number_format($totalReservas) }}</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">Aprobadas</h3>
                <svg class="w-8 h-8 opacity-80" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="text-4xl font-bold">{{ number_format($reservasAprobadas) }}</p>
            <p class="text-sm opacity-90 mt-1">{{ $tasaAprobacion }}% tasa de aprobación</p>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">Pendientes</h3>
                <svg class="w-8 h-8 opacity-80" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="text-4xl font-bold">{{ number_format($reservasPendientes) }}</p>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">Rechazadas</h3>
                <svg class="w-8 h-8 opacity-80" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="text-4xl font-bold">{{ number_format($reservasRechazadas) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
        <svg class="w-7 h-7 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Análisis de Aprobación y Rechazo
    </h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Gráfico de Dona -->
        <div class="lg:col-span-1">
            <div class="bg-gray-50 rounded-lg p-6 h-full flex flex-col justify-center">
                <canvas id="chartAprobacionRechazo" class="max-h-64"></canvas>
                
                <!-- Leyenda personalizada -->
                <div class="mt-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                            <span class="text-sm font-medium">Aprobadas</span>
                        </div>
                        <span class="text-sm font-bold">{{ $reservasAprobadas }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                            <span class="text-sm font-medium">Rechazadas</span>
                        </div>
                        <span class="text-sm font-bold">{{ $reservasRechazadas }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                            <span class="text-sm font-medium">Pendientes</span>
                        </div>
                        <span class="text-sm font-bold">{{ $reservasPendientes }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Tasas -->
        <div class="lg:col-span-2 w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 h-full w-full">
                <!-- Tasa de Aprobación -->
                <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg p-6 text-white shadow-lg transform hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold opacity-90">Tasa de Aprobación</h3>
                        <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex items-baseline">
                        <p class="text-5xl font-bold">{{ $tasaAprobacion }}%</p>
                    </div>
                    <p class="mt-4 text-sm opacity-90">
                        {{ $reservasAprobadas }} de {{ $totalReservas }} reservas aprobadas
                    </p>
                    
                    <!-- Barra de progreso -->
                    <div class="mt-4 bg-white bg-opacity-30 rounded-full h-3 overflow-hidden">
                        <div class="bg-white h-full rounded-full transition-all duration-500" 
                             style="width: {{ $tasaAprobacion }}%"></div>
                    </div>

                    <!-- Indicador de tendencia -->
                    @if($tasaAprobacion >= 80)
                        <div class="mt-4 flex items-center text-sm">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">Excelente desempeño</span>
                        </div>
                    @elseif($tasaAprobacion >= 60)
                        <div class="mt-4 flex items-center text-sm">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">Buen desempeño</span>
                        </div>
                    @else
                        <div class="mt-4 flex items-center text-sm">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">Requiere atención</span>
                        </div>
                    @endif
                </div>

                <!-- Tasa de Rechazo -->
                <div class="bg-gradient-to-br from-red-400 to-red-600 rounded-lg p-6 text-white shadow-lg transform hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold opacity-90">Tasa de Rechazo</h3>
                        <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex items-baseline">
                        <p class="text-5xl font-bold">{{ $tasaRechazo }}%</p>
                    </div>
                    <p class="mt-4 text-sm opacity-90">
                        {{ $reservasRechazadas }} de {{ $totalReservas }} reservas rechazadas
                    </p>
                    
                    <!-- Barra de progreso -->
                    <div class="mt-4 bg-white bg-opacity-30 rounded-full h-3 overflow-hidden">
                        <div class="bg-white h-full rounded-full transition-all duration-500" 
                             style="width: {{ $tasaRechazo }}%"></div>
                    </div>

                    <!-- Indicador de tendencia -->
                    @if($tasaRechazo <= 10)
                        <div class="mt-4 flex items-center text-sm">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">Tasa baja (óptimo)</span>
                        </div>
                    @elseif($tasaRechazo <= 25)
                        <div class="mt-4 flex items-center text-sm">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">Tasa moderada</span>
                        </div>
                    @else
                        <div class="mt-4 flex items-center text-sm">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">Tasa alta (revisar)</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Análisis adicional - REALMENTE AFUERA DEL GRID DE 3 COLUMNAS -->
    <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded mt-6">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-indigo-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="text-sm text-indigo-800">
                <p class="font-semibold mb-1">Análisis del Periodo:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Total de solicitudes procesadas: <strong>{{ $reservasAprobadas + $reservasRechazadas }}</strong></li>
                    <li>Solicitudes pendientes: <strong>{{ $reservasPendientes }}</strong> ({{ $totalReservas > 0 ? round(($reservasPendientes / $totalReservas) * 100, 1) : 0 }}%)</li>
                    @if($tasaRechazo > 30)
                        <li class="text-red-700 font-semibold"> Alta tasa de rechazo - revisar criterios de aprobación</li>
                    @endif
                    @if($reservasPendientes > ($totalReservas * 0.3))
                        <li class="text-yellow-700 font-semibold"> Muchas solicitudes pendientes - agilizar proceso</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Script para el gráfico de dona -->
<script>
// Gráfico de Aprobación vs Rechazo (Dona)
const ctxAprobacionRechazo = document.getElementById('chartAprobacionRechazo');
if (ctxAprobacionRechazo) {
    new Chart(ctxAprobacionRechazo.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Aprobadas', 'Rechazadas', 'Pendientes'],
            datasets: [{
                data: [{{ $reservasAprobadas }}, {{ $reservasRechazadas }}, {{ $reservasPendientes }}],
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',   // Verde
                    'rgba(239, 68, 68, 0.8)',   // Rojo
                    'rgba(234, 179, 8, 0.8)'    // Amarillo
                ],
                borderColor: [
                    'rgba(34, 197, 94, 1)',
                    'rgba(239, 68, 68, 1)',
                    'rgba(234, 179, 8, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false  // Usamos nuestra leyenda personalizada
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}
</script>


    <!-- Gráficos Interactivos Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <!-- Gráfico: Deportes Populares (Pastel) -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                </svg>
                Deportes más Populares
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="chartDeportes"></canvas>
            </div>
        </div>

        <!-- Gráfico: Recintos (Barra Horizontal) -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                Recintos más Solicitados
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="chartRecintos"></canvas>
            </div>
        </div>

        <!-- Gráfico: Horarios (Línea) -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                Demanda por Horario
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="chartHorarios"></canvas>
            </div>
        </div>

        <!-- Gráfico: Estado de Reservas (Dónut) -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                Estado de Reservas
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="chartEstados"></canvas>
            </div>
        </div>

    </div>

    <!-- Gráfico: Días de la Semana -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
            </svg>
            Actividad por Día de la Semana
        </h3>
        <div style="position: relative; height: 350px;">
            <canvas id="chartDias"></canvas>
        </div>
    </div>

    <!-- Gráfico y Tabla: Organizaciones más Activas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                </svg>
                Top 10 Organizaciones más Activas
            </h3>
            <div style="position: relative; height: 350px;">
                <canvas id="chartOrganizaciones"></canvas>
            </div>
        </div>

        <!-- Ranking Lateral -->
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg shadow-lg p-6 border border-indigo-200">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Ranking</h3>
            @if($organizacionesMasActivas->count() > 0)
                <div class="space-y-3">
                    @foreach($organizacionesMasActivas->take(5) as $index => $org)
                    <div class="flex items-center p-3 bg-white rounded-lg hover:shadow-md transition-shadow">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                            {{ $index < 3 ? 'bg-gradient-to-br from-yellow-400 to-yellow-500 text-white font-bold' : 'bg-gray-200 text-gray-700 font-semibold' }}">
                            {{ $index + 1 }}
                        </span>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $org->nombre_organizacion }}</p>
                            <p class="text-xs text-gray-600">{{ $org->total }} reservas</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No hay datos disponibles</p>
            @endif
        </div>
    </div>

    <!-- SECCIÓN 6.2: REPORTES DE CUMPLIMIENTO -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
                Reportes de Cumplimiento
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
                     Registro de Asistencia
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-3 bg-white rounded-lg border border-blue-200">
                        <p class="text-2xl font-bold text-blue-600">{{ $totalReservas }}</p>
                        <p class="text-xs text-gray-600">Total registrado</p>
                    </div>
                    <div class="text-center p-3 bg-white rounded-lg border border-green-200">
                        <p class="text-2xl font-bold text-green-600">{{ round(($reservasAprobadas / max($totalReservas, 1)) * 100) }}%</p>
                        <p class="text-xs text-gray-600">Asistencia</p>
                    </div>
                    <div class="text-center p-3 bg-white rounded-lg border border-yellow-200">
                        <p class="text-2xl font-bold text-yellow-600">{{ $reservasPendientes }}</p>
                        <p class="text-xs text-gray-600">Pendientes</p>
                    </div>
                    <div class="text-center p-3 bg-white rounded-lg border border-red-200">
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
                 Descargar Reportes
            </h2>
            <p class="text-gray-600 text-sm">Exporta tus datos en diferentes formatos</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Exportar a Excel -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 p-6 rounded-lg text-center hover:shadow-lg transition-shadow cursor-pointer">
                <div class="flex justify-center mb-4">
                    <svg class="w-12 h-12 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-green-800 mb-2">Exportar Excel</h3>
                <p class="text-sm text-gray-700 mb-4">Descarga todos los datos en formato XLS</p>
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
                <h3 class="text-lg font-semibold text-red-800 mb-2">Exportar PDF</h3>
                <p class="text-sm text-gray-700 mb-4">Descarga informe completo en PDF</p>
                <a href="{{ route('admin.estadisticas.exportar-pdf', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}" class="w-full block bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Descargar PDF
                </a>
            </div>

            <!-- Instrucciones -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 p-6 rounded-lg">
                <div class="flex justify-center mb-4">
                    <svg class="w-12 h-12 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Información</h3>
                <p class="text-sm text-gray-700">Los reportes incluyen todos los datos del período seleccionado. Puedes aplicar filtros de fechas antes de descargar.</p>
            </div>
        </div>
    </div>

    <!-- SECCIÓN: REPORTES ESPECIALES -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
                Reportes Especiales
            </h2>
            <p class="text-gray-600 text-sm">Accede a análisis detallados y reportes personalizados</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Reporte Histórico por Organización -->
            <a href="{{ route('admin.reportes.organizacion') }}" 
            class="group bg-gradient-to-br from-indigo-50 to-purple-50 border-2 border-indigo-200 p-6 rounded-lg hover:shadow-xl transition-all transform hover:-translate-y-1">
                <div class="flex justify-center mb-4">
                    <div class="bg-indigo-100 p-4 rounded-full group-hover:bg-indigo-200 transition-colors">
                        <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-indigo-900 mb-2 text-center">Reporte Histórico por Organización</h3>
                <p class="text-sm text-gray-700 text-center mb-4">
                    Análisis completo del historial de uso de cada organización con gráficos interactivos
                </p>
                <div class="flex items-center justify-center text-indigo-600 font-medium text-sm group-hover:text-indigo-800">
                    <span>Acceder al Reporte</span>
                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </a>

            <!-- Reporte por Recinto (Futuro) -->
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 border-2 border-blue-200 p-6 rounded-lg opacity-75">
                <div class="flex justify-center mb-4">
                    <div class="bg-blue-100 p-4 rounded-full">
                        <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-blue-900 mb-2 text-center">Reporte por Recinto</h3>
                <p class="text-sm text-gray-700 text-center mb-4">
                    Estadísticas de uso y mantenimiento por instalación deportiva
                </p>
                <div class="flex items-center justify-center">
                    <span class="inline-block bg-gray-200 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold">
                        Próximamente
                    </span>
                </div>
            </div>

            <!-- Análisis de Tendencias (Futuro) -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 p-6 rounded-lg opacity-75">
                <div class="flex justify-center mb-4">
                    <div class="bg-green-100 p-4 rounded-full">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-green-900 mb-2 text-center">Análisis de Tendencias</h3>
                <p class="text-sm text-gray-700 text-center mb-4">
                    Predicciones y patrones de uso para mejor planificación
                </p>
                <div class="flex items-center justify-center">
                    <span class="inline-block bg-gray-200 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold">
                        Próximamente
                    </span>
                </div>
            </div>

        </div>

        <!-- Info adicional -->
        <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Tip:</strong> El reporte histórico por organización te permite analizar en detalle el comportamiento y uso de cada entidad registrada en el sistema.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparativas Entre Períodos -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
            </svg>
             Resumen y Tendencias
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
                <h3 class="font-semibold text-blue-800 mb-4"> Métricas Clave</h3>
                <table class="w-full text-sm">
                    <tbody class="divide-y">
                        <tr class="hover:bg-blue-100">
                            <td class="px-4 py-3">Total Reservas</td>
                            <td class="px-4 py-3 text-right font-semibold">{{ $totalReservas }}</td>
                        </tr>
                        <tr class="hover:bg-blue-100">
                            <td class="px-4 py-3">Tasa de Aprobación</td>
                            <td class="px-4 py-3 text-right font-semibold">{{ $tasaAprobacion }}%</td>
                        </tr>
                        <tr class="hover:bg-blue-100">
                            <td class="px-4 py-3">Organizaciones Activas</td>
                            <td class="px-4 py-3 text-right font-semibold">{{ $organizacionesMasActivas->count() }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-lg">
                <h3 class="font-semibold text-green-800 mb-4"> Observaciones</h3>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414-1.414L13.586 7H12z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-gray-700">Sistema funcionando <strong>óptimamente</strong></span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414-1.414L13.586 7H12z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-gray-700">Tasa de cumplimiento <strong>estable</strong></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>

<!-- Script para Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
    // Configuración global de Chart.js
    Chart.defaults.font.family = "'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif";

    // Gráfico: Deportes Populares (Pastel)
    const ctxDeportes = document.getElementById('chartDeportes')?.getContext('2d');
    if (ctxDeportes) {
        new Chart(ctxDeportes, {
            type: 'doughnut',
            data: {
                labels: [
                    @foreach($deportesPopulares as $deporte)
                        '{{ $deporte->deporte ?? "No especificado" }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($deportesPopulares as $deporte)
                            {{ $deporte->total }},
                        @endforeach
                    ],
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                        '#FF9F40', '#FF6384', '#C9CBCF'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 12 }
                        }
                    }
                }
            }
        });
    }

    // Gráfico: Recintos (Barra Horizontal)
    const ctxRecintos = document.getElementById('chartRecintos')?.getContext('2d');
    if (ctxRecintos) {
        new Chart(ctxRecintos, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($recintosMasSolicitados as $recinto)
                        '{{ $recinto->nombre }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Reservas',
                    data: [
                        @foreach($recintosMasSolicitados as $recinto)
                            {{ $recinto->total }},
                        @endforeach
                    ],
                    backgroundColor: [
                        '#10B981', '#34D399', '#6EE7B7', '#A7F3D0', '#D1FAE5'
                    ],
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });
    }

    // Gráfico: Horarios (Línea)
    const ctxHorarios = document.getElementById('chartHorarios')?.getContext('2d');
    if (ctxHorarios) {
        new Chart(ctxHorarios, {
            type: 'line',
            data: {
                labels: [
                    @foreach($horariosPopulares as $horario)
                        '{{ sprintf("%02d:00", $horario->hora) }} - {{ sprintf("%02d:00", $horario->hora + 1) }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Reservas por Horario',
                    data: [
                        @foreach($horariosPopulares as $horario)
                            {{ $horario->total }},
                        @endforeach
                    ],
                    borderColor: '#F97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#F97316',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Gráfico: Estado de Reservas (Dónut)
    const ctxEstados = document.getElementById('chartEstados')?.getContext('2d');
    if (ctxEstados) {
        new Chart(ctxEstados, {
            type: 'doughnut',
            data: {
                labels: ['Aprobadas', 'Pendientes', 'Rechazadas'],
                datasets: [{
                    data: [{{ $reservasAprobadas }}, {{ $reservasPendientes }}, {{ $reservasRechazadas }}],
                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Gráfico: Días de la Semana (Barra)
    const ctxDias = document.getElementById('chartDias')?.getContext('2d');
    if (ctxDias) {
        new Chart(ctxDias, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($diasSemanaPopulares as $dia)
                        '{{ $dia->dia_nombre }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Reservas',
                    data: [
                        @foreach($diasSemanaPopulares as $dia)
                            {{ $dia->total }},
                        @endforeach
                    ],
                    backgroundColor: [
                        '#A78BFA', '#A78BFA', '#A78BFA', '#A78BFA', '#A78BFA', '#EC4899', '#EC4899'
                    ],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Gráfico: Organizaciones (Barra Horizontal)
    const ctxOrganizaciones = document.getElementById('chartOrganizaciones')?.getContext('2d');
    if (ctxOrganizaciones) {
        new Chart(ctxOrganizaciones, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($organizacionesMasActivas->take(10) as $org)
                        '{{ $org->nombre_organizacion }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Total Reservas',
                    data: [
                        @foreach($organizacionesMasActivas->take(10) as $org)
                            {{ $org->total }},
                        @endforeach
                    ],
                    backgroundColor: '#6366F1',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });
    }
</script>

@endsection