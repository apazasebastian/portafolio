@extends('layouts.app')

@section('title', 'Panel Administrativo - Reservas Deportivas')

@section('content')

<!--  MENSAJE DE ERROR CON AUTO-CIERRE  -->
@if(session('error'))
    <div id="error-alert" class="fixed top-4 right-4 z-50 max-w-md animate-fade-in">
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg shadow-2xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-bold text-red-800">Acceso Denegado</h3>
                        <p class="text-sm text-red-700 mt-1">{{ session('error') }}</p>
                        <p class="text-xs text-red-600 mt-1">Cerrando en <span id="timer">5</span>s...</p>
                    </div>
                </div>
                <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                        class="text-red-400 hover:text-red-600 ml-4">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        let seconds = 5;
        const timer = document.getElementById('timer');
        const alert = document.getElementById('error-alert');
        
        const countdown = setInterval(() => {
            seconds--;
            timer.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(countdown);
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 1000);
    </script>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
@endif

<div class="container mx-auto px-4 py-8">
    <!-- Header con Título y Botón de Exportación -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Panel Administrativo</h1>
                <p class="text-gray-600">Gestión de reservas de recintos deportivos</p>
            </div>
            
            <div>
                <a href="{{ route('admin.dashboard.exportar', request()->query()) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exportar Reservas
                </a>
            </div>
        </div>
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

    <!--  CALENDARIO INTERACTIVO CON VUE.JS (NUEVO)  -->
    <div class="bg-white rounded-lg shadow-md mb-8" id="calendar-app">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
            <!-- CALENDARIO (2/3 del espacio) -->
            <div class="lg:col-span-2">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Calendario de Reservas</h2>
                    <div class="flex items-center gap-2">
                        <button @click="prevMonth" class="p-2 hover:bg-gray-100 rounded transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <span class="text-base font-semibold text-gray-700 min-w-[140px] text-center">
                            @{{ currentMonthName }} @{{ currentYear }}
                        </span>
                        <button @click="nextMonth" class="p-2 hover:bg-gray-100 rounded transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Días de la semana -->
                <div class="grid grid-cols-7 gap-1 mb-1">
                    <div v-for="day in ['D', 'L', 'M', 'Mi', 'J', 'V', 'S']" 
                         class="text-center font-semibold text-gray-600 text-sm py-1">
                        @{{ day }}
                    </div>
                </div>

                <!-- Días del mes -->
                <div class="grid grid-cols-7 gap-1">
                    <div v-for="day in calendarDays" 
                         :key="day.date"
                         @click="selectDay(day)"
                         :class="[
                             'min-h-[60px] p-1 rounded border cursor-pointer transition-all',
                             day.isCurrentMonth ? 'bg-white hover:bg-blue-50 border-gray-200' : 'bg-gray-50 text-gray-400 border-gray-100',
                             day.isToday ? 'border-2 border-blue-500 bg-blue-50' : '',
                             selectedDay && selectedDay.date === day.date ? 'ring-2 ring-blue-400 bg-blue-50' : '',
                             day.reservas.length > 0 ? 'font-semibold' : ''
                         ]">
                        <div class="text-xs mb-1" :class="day.isToday ? 'text-blue-600 font-bold' : ''">
                            @{{ day.day }}
                        </div>
                        <div v-if="day.reservas.length > 0" class="flex flex-col gap-0.5">
                            <div class="flex gap-0.5">
                                <div v-if="day.pendientes > 0" 
                                     class="w-2 h-2 rounded-full bg-yellow-400" 
                                     :title="`${day.pendientes} pendiente(s)`"></div>
                                <div v-if="day.aprobadas > 0" 
                                     class="w-2 h-2 rounded-full bg-green-400"
                                     :title="`${day.aprobadas} aprobada(s)`"></div>
                            </div>
                            <div class="text-[10px] text-gray-600">
                                @{{ day.reservas.length }} reserva@{{ day.reservas.length !== 1 ? 's' : '' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leyenda -->
                <div class="flex items-center gap-4 mt-4 text-xs text-gray-600">
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                        <span>Pendientes</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        <span>Aprobadas</span>
                    </div>
                </div>
            </div>

            <!-- PANEL LATERAL DE RESERVAS (1/3 del espacio) -->
            <div class="lg:col-span-1">
                <div class="sticky top-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <span v-if="selectedDay">
                            Reservas del @{{ selectedDay.day }} de @{{ currentMonthName }}
                        </span>
                        <span v-else class="text-gray-500">
                            Selecciona un día
                        </span>
                    </h3>

                    <!-- Filtros rápidos -->
                    <div v-if="selectedDay && selectedDay.reservas.length > 0" class="flex gap-2 mb-4">
                        <button @click="filtroEstado = 'todas'" 
                                :class="['px-3 py-1 text-xs font-medium rounded transition-colors',
                                         filtroEstado === 'todas' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']">
                            Todas (@{{ selectedDay.reservas.length }})
                        </button>
                        <button v-if="selectedDay.pendientes > 0"
                                @click="filtroEstado = 'pendiente'" 
                                :class="['px-3 py-1 text-xs font-medium rounded transition-colors',
                                         filtroEstado === 'pendiente' ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200']">
                            Pendientes (@{{ selectedDay.pendientes }})
                        </button>
                        <button v-if="selectedDay.aprobadas > 0"
                                @click="filtroEstado = 'aprobada'" 
                                :class="['px-3 py-1 text-xs font-medium rounded transition-colors',
                                         filtroEstado === 'aprobada' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200']">
                            Aprobadas (@{{ selectedDay.aprobadas }})
                        </button>
                    </div>

                    <!-- Lista de reservas del día -->
                    <div v-if="selectedDay" class="space-y-2 max-h-[400px] overflow-y-auto">
                        <div v-if="reservasFiltradas.length === 0" class="text-center py-8 text-gray-500 text-sm">
                            No hay reservas para este día
                        </div>

                        <div v-for="reserva in reservasFiltradas" 
                             :key="reserva.id"
                             class="bg-gray-50 rounded-lg p-3 border border-gray-200 hover:border-blue-300 transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <div class="font-medium text-sm text-gray-900">
                                    @{{ reserva.organizacion || 'Sin organización' }}
                                </div>
                                <span :class="[
                                    'px-2 py-0.5 text-xs font-medium rounded-full',
                                    reserva.estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' :
                                    reserva.estado === 'aprobada' ? 'bg-green-100 text-green-800' :
                                    'bg-red-100 text-red-800'
                                ]">
                                    @{{ reserva.estado }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-600 space-y-1">
                                <div class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    @{{ reserva.recinto }}
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    @{{ reserva.hora_inicio }} - @{{ reserva.hora_fin }}
                                </div>
                            </div>
                            <a :href="`/admin/reservas/${reserva.id}`" 
                               class="mt-2 block text-center text-xs text-blue-600 hover:text-blue-800 font-medium">
                                Ver detalles →
                            </a>
                        </div>
                    </div>

                    <div v-else class="text-center py-8 text-gray-400 text-sm">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Selecciona un día para ver las reservas
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Reservas Completa (con tabla) -->
    <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">Gestión de Reservas</h2>
                    <p class="text-sm text-gray-600 mt-1">Visualiza y administra todas las reservas del sistema</p>
                </div>
            </div>
        </div>

        


        <!-- Filtros Avanzados Mejorados -->
<div class="px-6 py-4 bg-white border-b border-gray-200">
    <form method="GET" action="{{ route('admin.dashboard') }}" class="space-y-4">
        <input type="hidden" name="filtro" value="{{ request('filtro', 'todas') }}">
        
        <!-- Primera fila: Estado, Recinto, Deporte, Fecha -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Estado -->
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                    Estado
                </label>
                <select name="estado" id="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="aprobada" {{ request('estado') === 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                    <option value="rechazada" {{ request('estado') === 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                    <option value="cancelada" {{ request('estado') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>

            <!-- Recinto -->
            <div>
                <label for="recinto_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Recinto
                </label>
                <select name="recinto_id" id="recinto_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos los recintos</option>
                    @foreach($recintos as $recinto)
                        <option value="{{ $recinto->id }}" {{ request('recinto_id') == $recinto->id ? 'selected' : '' }}>
                            {{ $recinto->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Deporte -->
            <div>
                <label for="deporte" class="block text-sm font-medium text-gray-700 mb-1">
                    Deporte
                </label>
                <select name="deporte" id="deporte" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos los deportes</option>
                    <option value="Fútbol" {{ request('deporte') == 'Fútbol' ? 'selected' : '' }}>Fútbol</option>
                    <option value="Básquetbol" {{ request('deporte') == 'Básquetbol' ? 'selected' : '' }}>Básquetbol</option>
                    <option value="Vóleibol" {{ request('deporte') == 'Vóleibol' ? 'selected' : '' }}>Vóleibol</option>
                    <option value="Tenis" {{ request('deporte') == 'Tenis' ? 'selected' : '' }}>Tenis</option>
                    <option value="Natación" {{ request('deporte') == 'Natación' ? 'selected' : '' }}>Natación</option>
                </select>
            </div>

            <!-- Fecha -->
            <div>
                <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">
                    Fecha
                </label>
                <input type="date" name="fecha" id="fecha" value="{{ request('fecha') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>

        <!-- Segunda fila: Búsqueda por RUT y Organización -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
            <!-- Búsqueda por RUT CON FORMATEO AUTOMÁTICO Y SOPORTE PARA K -->
            <div>
                <label for="buscar_rut" class="block text-sm font-medium text-gray-700 mb-1">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar por RUT
                </label>
                <input type="text" name="buscar_rut" id="buscar_rut" 
                       placeholder="ej: 21.284.335-0" 
                       value="{{ request('buscar_rut') }}"
                       maxlength="12"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       title="Solo números y letra K">
                <p class="text-xs text-gray-500 mt-1">Máximo 9 dígitos (se formatea automáticamente)</p>
            </div>

            <!-- Búsqueda por Organización -->
            <div>
                <label for="buscar_organizacion" class="block text-sm font-medium text-gray-700 mb-1">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar por Organización
                </label>
                <input type="text" name="buscar_organizacion" id="buscar_organizacion" 
                       placeholder="Ingrese parte del nombre" 
                       value="{{ request('buscar_organizacion') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>

        <!-- Tercera fila: Botones de acción -->
        <div class="flex gap-2 pt-4 border-t border-gray-200">
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                Filtrar
            </button>
            <a href="{{ route('admin.dashboard') }}" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                Limpiar
            </a>
        </div>

        <!-- Indicadores de filtros activos -->
        @if(request()->filled('estado') || request()->filled('recinto_id') || request()->filled('deporte') || request()->filled('fecha') || 
            request()->filled('buscar_rut') || request()->filled('buscar_organizacion'))
            <div class="flex flex-wrap items-center gap-2 pt-4 border-t border-gray-200">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v2H3V3zm0 3h14v11a1 1 0 01-1 1H4a1 1 0 01-1-1V6z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Filtros activos:</span>
                </div>
                
                @if(request('estado'))
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full border border-blue-200">
                         Estado: {{ ucfirst(request('estado')) }}
                    </span>
                @endif
                
                @if(request('recinto_id'))
                    @php
                        $recinto = $recintos->find(request('recinto_id'));
                    @endphp
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full border border-green-200">
                         {{ $recinto->nombre ?? 'Recinto' }}
                    </span>
                @endif
                
                @if(request('deporte'))
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full border border-purple-200">
                         {{ request('deporte') }}
                    </span>
                @endif
                
                @if(request('fecha'))
                    <span class="px-3 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full border border-orange-200">
                         {{ \Carbon\Carbon::parse(request('fecha'))->format('d/m/Y') }}
                    </span>
                @endif
                
                @if(request('buscar_rut'))
                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full border border-red-200">
                         RUT: {{ request('buscar_rut') }}
                    </span>
                @endif
                
                @if(request('buscar_organizacion'))
                    <span class="px-3 py-1 bg-pink-100 text-pink-800 text-xs font-medium rounded-full border border-pink-200">
                         {{ request('buscar_organizacion') }}
                    </span>
                @endif
            </div>
        @endif
    </form>
</div>

<!-- Script para formatear RUT automáticamente con soporte para K -->
<script>
document.getElementById('buscar_rut').addEventListener('input', function(e) {
    let value = e.target.value;
    
    // Convertir K minúscula a mayúscula, y remover todo lo que no sea números o K
    value = value.toUpperCase().replace(/[^0-9K]/g, '');
    
    // La K solo puede estar al final (dígito verificador)
    // Limitar a máximo 9 caracteres (8 números + 1 K)
    if (value.length > 9) {
        value = value.slice(0, 9);
    }
    
    // Verificar que K solo esté al final
    let lastChar = value.charAt(value.length - 1);
    if (lastChar === 'K' && value.length > 1) {
        // Validar que antes de K solo haya números
        let beforeK = value.slice(0, -1);
        if (beforeK.match(/^[0-9]{1,8}$/)) {
            // Es válido
        } else {
            // Remover K si hay caracteres inválidos antes
            value = beforeK;
        }
    } else if (value.includes('K') && lastChar !== 'K') {
        // Si K está en medio, removerla
        value = value.replace(/K/g, '');
    }
    
    // Formatear: XX.XXX.XXX-X (o XX.XXX.XXX-K)
    let formatted = '';
    if (value.length > 0) {
        if (value.length <= 2) {
            formatted = value;
        } else if (value.length <= 5) {
            formatted = value.slice(0, 2) + '.' + value.slice(2);
        } else if (value.length <= 8) {
            formatted = value.slice(0, 2) + '.' + value.slice(2, 5) + '.' + value.slice(5);
        } else {
            formatted = value.slice(0, 2) + '.' + value.slice(2, 5) + '.' + value.slice(5, 8) + '-' + value.slice(8);
        }
    }
    
    e.target.value = formatted;
});

// Prevenir pegar texto que no sea números o K
document.getElementById('buscar_rut').addEventListener('paste', function(e) {
    e.preventDefault();
    const text = (e.clipboardData || window.clipboardData).getData('text');
    
    // Convertir a mayúsculas y remover caracteres inválidos
    let numbers = text.toUpperCase().replace(/[^0-9K]/g, '').slice(0, 9);
    
    // Validar que K solo esté al final
    if (numbers.includes('K')) {
        if (numbers.charAt(numbers.length - 1) === 'K') {
            // K está al final, validar
            let beforeK = numbers.slice(0, -1);
            if (!beforeK.match(/^[0-9]{1,8}$/)) {
                numbers = numbers.replace(/K/g, '');
            }
        } else {
            // K está en medio, remover
            numbers = numbers.replace(/K/g, '');
        }
    }
    
    let formatted = '';
    if (numbers.length > 0) {
        if (numbers.length <= 2) {
            formatted = numbers;
        } else if (numbers.length <= 5) {
            formatted = numbers.slice(0, 2) + '.' + numbers.slice(2);
        } else if (numbers.length <= 8) {
            formatted = numbers.slice(0, 2) + '.' + numbers.slice(2, 5) + '.' + numbers.slice(5);
        } else {
            formatted = numbers.slice(0, 2) + '.' + numbers.slice(2, 5) + '.' + numbers.slice(5, 8) + '-' + numbers.slice(8);
        }
    }
    
    this.value = formatted;
});
</script>

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
    <div class="grid grid-cols-1 md:grid-cols-{{ auth()->user()->role === 'jefe_recintos' ? '5' : '4' }} gap-6">
        <a href="{{ route('calendario') }}" class="bg-white hover:bg-gray-50 p-6 rounded-lg text-center transition-all shadow-md hover:shadow-lg border border-gray-200">
            <svg class="w-12 h-12 mx-auto mb-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="font-semibold text-gray-800">Calendario Público</h3>
            <p class="text-gray-600 text-sm mt-1">Ver disponibilidad</p>
        </a>
        @if(auth()->user()->role !== 'encargado_recinto')
            <a href="{{ route('admin.recintos.index') }}" class="bg-white hover:bg-gray-50 p-6 rounded-lg text-center transition-all shadow-md hover:shadow-lg border border-gray-200">
                <svg class="w-12 h-12 mx-auto mb-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="font-semibold text-gray-800">Gestión de Recintos</h3>
                <p class="text-gray-600 text-sm mt-1">Administrar instalaciones</p>
            </a>
        @endif
        <a href="{{ route('admin.estadisticas.index') }}" class="bg-white hover:bg-gray-50 p-6 rounded-lg text-center transition-all shadow-md hover:shadow-lg border border-gray-200">
            <svg class="w-12 h-12 mx-auto mb-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <h3 class="font-semibold text-gray-800">Estadísticas</h3>
            <p class="text-gray-600 text-sm mt-1">Reportes y análisis</p>
        </a>

        @if(auth()->user()->role === 'jefe_recintos')
            <a href="{{ route('admin.auditoria.index') }}" class="bg-white hover:bg-gray-50 p-6 rounded-lg text-center transition-all shadow-md hover:shadow-lg border border-gray-200">
                <svg class="w-12 h-12 mx-auto mb-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="font-semibold text-gray-800">Auditoría</h3>
                <p class="text-gray-600 text-sm mt-1">Registro de acciones</p>
            </a>
        @endif
        
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

<!--  SCRIPT DE VUE.JS PARA EL CALENDARIO (NUEVO)  -->
<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>
<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            currentMonth: new Date().getMonth(),
            currentYear: new Date().getFullYear(),
            selectedDay: null,
            filtroEstado: 'todas',
            reservas: @json($todasReservasCalendario ?? []),
            meses: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
        }
    },
    computed: {
        currentMonthName() {
            return this.meses[this.currentMonth];
        },
        calendarDays() {
            const year = this.currentYear;
            const month = this.currentMonth;
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const prevLastDay = new Date(year, month, 0);
            const firstDayOfWeek = firstDay.getDay();
            const lastDateOfMonth = lastDay.getDate();
            const lastDateOfPrevMonth = prevLastDay.getDate();
            
            const days = [];
            
            // Días del mes anterior
            for (let i = firstDayOfWeek - 1; i >= 0; i--) {
                const day = lastDateOfPrevMonth - i;
                const date = new Date(year, month - 1, day);
                days.push(this.createDayObject(date, false));
            }
            
            // Días del mes actual
            for (let day = 1; day <= lastDateOfMonth; day++) {
                const date = new Date(year, month, day);
                days.push(this.createDayObject(date, true));
            }
            
            // Días del siguiente mes
            const remainingDays = 35 - days.length;
            for (let day = 1; day <= remainingDays; day++) {
                const date = new Date(year, month + 1, day);
                days.push(this.createDayObject(date, false));
            }
            
            return days;
        },
        reservasFiltradas() {
            if (!this.selectedDay) return [];
            
            if (this.filtroEstado === 'todas') {
                return this.selectedDay.reservas;
            }
            
            return this.selectedDay.reservas.filter(r => r.estado === this.filtroEstado);
        }
    },
    methods: {
        createDayObject(date, isCurrentMonth) {
            const dateStr = date.toISOString().split('T')[0];
            const today = new Date().toISOString().split('T')[0];
            const reservasDelDia = this.reservas.filter(r => r.fecha === dateStr);
            
            return {
                date: dateStr,
                day: date.getDate(),
                isCurrentMonth,
                isToday: dateStr === today,
                reservas: reservasDelDia,
                pendientes: reservasDelDia.filter(r => r.estado === 'pendiente').length,
                aprobadas: reservasDelDia.filter(r => r.estado === 'aprobada').length
            };
        },
        prevMonth() {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear--;
            } else {
                this.currentMonth--;
            }
            this.selectedDay = null;
        },
        nextMonth() {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear++;
            } else {
                this.currentMonth++;
            }
            this.selectedDay = null;
        },
        selectDay(day) {
            if (day.isCurrentMonth) {
                this.selectedDay = day;
                this.filtroEstado = 'todas';
            }
        }
    }
}).mount('#calendar-app');
</script>
@endsection