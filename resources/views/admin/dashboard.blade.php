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

<div class="max-w-7xl mx-auto px-6 lg:px-8 py-8">
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-xl shadow-sm p-8">
            <div class="text-4xl font-bold text-gray-900 mb-2">{{ $reservasPendientes }}</div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Reservas Pendientes</div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-xl shadow-sm p-8">
            <div class="text-4xl font-bold text-gray-900 mb-2">{{ $reservasHoy }}</div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Reservas Hoy</div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-xl shadow-sm p-8">
            <div class="text-4xl font-bold text-gray-900 mb-2">{{ $reservasEstesMes }}</div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Este Mes</div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-xl shadow-sm p-8">
            <div class="text-4xl font-bold text-gray-900 mb-2">{{ $recintosActivos }}</div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Recintos Activos</div>
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

        


        
        <!-- Filtros Avanzados Minimalistas -->
        <div class="px-8 py-8 bg-white mb-8 rounded-xl shadow-sm">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="space-y-8">
                <input type="hidden" name="filtro" value="{{ request('filtro', 'todas') }}">
                
                <!-- Headers Labels Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Estado -->
                    <div class="group">
                        <label for="estado" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                            Estado
                        </label>
                        <select name="estado" id="estado" class="w-full py-2 border-b-2 border-gray-200 bg-transparent text-gray-800 font-medium focus:border-blue-900 focus:outline-none transition-colors appearance-none cursor-pointer">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="aprobada" {{ request('estado') === 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                            <option value="rechazada" {{ request('estado') === 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                            <option value="cancelada" {{ request('estado') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>

                    <!-- Recinto -->
                    <div class="group">
                        <label for="recinto_id" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                            Recinto
                        </label>
                        <select name="recinto_id" id="recinto_id" class="w-full py-2 border-b-2 border-gray-200 bg-transparent text-gray-800 font-medium focus:border-blue-900 focus:outline-none transition-colors appearance-none cursor-pointer">
                            <option value="">Todos los recintos</option>
                            @foreach($recintos as $recinto)
                                <option value="{{ $recinto->id }}" {{ request('recinto_id') == $recinto->id ? 'selected' : '' }}>
                                    {{ $recinto->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Deporte -->
                    <div class="group">
                        <label for="deporte" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                            Deporte
                        </label>
                        <select name="deporte" id="deporte" class="w-full py-2 border-b-2 border-gray-200 bg-transparent text-gray-800 font-medium focus:border-blue-900 focus:outline-none transition-colors appearance-none cursor-pointer">
                            <option value="">Todos los deportes</option>
                            <option value="Fútbol" {{ request('deporte') == 'Fútbol' ? 'selected' : '' }}>Fútbol</option>
                            <option value="Básquetbol" {{ request('deporte') == 'Básquetbol' ? 'selected' : '' }}>Básquetbol</option>
                            <option value="Vóleibol" {{ request('deporte') == 'Vóleibol' ? 'selected' : '' }}>Vóleibol</option>
                            <option value="Tenis" {{ request('deporte') == 'Tenis' ? 'selected' : '' }}>Tenis</option>
                            <option value="Natación" {{ request('deporte') == 'Natación' ? 'selected' : '' }}>Natación</option>
                        </select>
                    </div>

                    <!-- Fecha -->
                    <div class="group">
                        <label for="fecha" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                            Fecha
                        </label>
                        <input type="date" name="fecha" id="fecha" value="{{ request('fecha') }}" 
                               class="w-full py-2 border-b-2 border-gray-200 bg-transparent text-gray-800 font-medium focus:border-blue-900 focus:outline-none transition-colors">
                    </div>
                </div>

                <!-- Segunda fila: Búsquedas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                    <!-- Búsqueda por RUT -->
                    <div class="group">
                        <label for="buscar_rut" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                            Buscar por RUT
                        </label>
                        <input type="text" name="buscar_rut" id="buscar_rut" 
                               placeholder="ej: 21.284.335-0" 
                               value="{{ request('buscar_rut') }}"
                               maxlength="12"
                               class="w-full py-2 border-b-2 border-gray-200 bg-transparent text-gray-800 font-medium placeholder-gray-300 focus:border-blue-900 focus:outline-none transition-colors"
                               title="Solo números y letra K">
                        <p class="text-[10px] text-gray-300 mt-1">Máximo 9 dígitos (se formatea automáticamente)</p>
                    </div>

                    <!-- Búsqueda por Organización -->
                    <div class="group">
                        <label for="buscar_organizacion" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                            Buscar por Organización
                        </label>
                        <input type="text" name="buscar_organizacion" id="buscar_organizacion" 
                               placeholder="Ingrese parte del nombre" 
                               value="{{ request('buscar_organizacion') }}"
                               class="w-full py-2 border-b-2 border-gray-200 bg-transparent text-gray-800 font-medium placeholder-gray-300 focus:border-blue-900 focus:outline-none transition-colors">
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex gap-4 pt-4 mt-4">
                    <button type="submit" class="px-8 py-3 bg-blue-900 hover:bg-blue-800 text-white font-bold text-sm tracking-wide rounded-lg transition-all shadow-md hover:shadow-lg">
                        Filtrar
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-sm tracking-wide rounded-lg transition-all">
                        Limpiar
                    </a>
                </div>

                <!-- Indicadores de filtros activos -->
                @if(request()->filled('estado') || request()->filled('recinto_id') || request()->filled('deporte') || request()->filled('fecha') || 
                    request()->filled('buscar_rut') || request()->filled('buscar_organizacion'))
                    <div class="flex flex-wrap items-center gap-2 pt-6 border-t border-gray-100">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mr-2">Filtros activos:</span>
                        
                        @if(request('estado'))
                            <span class="px-3 py-1 bg-blue-50 text-blue-800 text-xs font-bold rounded hover:bg-blue-100 transition-colors">
                                 ESTADO: {{ strtoupper(request('estado')) }}
                            </span>
                        @endif
                        
                        @if(request('recinto_id'))
                            @php
                                $recinto = $recintos->find(request('recinto_id'));
                            @endphp
                            <span class="px-3 py-1 bg-green-50 text-green-800 text-xs font-bold rounded hover:bg-green-100 transition-colors">
                                 {{ strtoupper($recinto->nombre ?? 'Recinto') }}
                            </span>
                        @endif
                        
                        @if(request('deporte'))
                            <span class="px-3 py-1 bg-purple-50 text-purple-800 text-xs font-bold rounded hover:bg-purple-100 transition-colors">
                                 {{ strtoupper(request('deporte')) }}
                            </span>
                        @endif
                        
                        @if(request('fecha'))
                            <span class="px-3 py-1 bg-orange-50 text-orange-800 text-xs font-bold rounded hover:bg-orange-100 transition-colors">
                                 {{ \Carbon\Carbon::parse(request('fecha'))->format('d/m/Y') }}
                            </span>
                        @endif
                        
                        @if(request('buscar_rut'))
                            <span class="px-3 py-1 bg-red-50 text-red-800 text-xs font-bold rounded hover:bg-red-100 transition-colors">
                                 RUT: {{ request('buscar_rut') }}
                            </span>
                        @endif
                        
                        @if(request('buscar_organizacion'))
                            <span class="px-3 py-1 bg-pink-50 text-pink-800 text-xs font-bold rounded hover:bg-pink-100 transition-colors">
                                 ORG: {{ strtoupper(request('buscar_organizacion')) }}
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
        <div class="overflow-x-auto bg-white rounded-xl shadow-sm">
            @if($reservas->count() > 0)
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Organización</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Recinto</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Fecha y Hora</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Deporte</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Personas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Estado</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($reservas as $reserva)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="text-sm text-gray-400 font-medium">#{{ $reserva->id }}</span>
                            </td>
                            
                            <td class="px-6 py-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gray-800 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">
                                            {{ strtoupper(substr($reserva->nombre_organizacion ?? $reserva->representante_nombre, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $reserva->nombre_organizacion ?? 'Sin organización' }}</div>
                                        <div class="text-xs text-gray-400">{{ $reserva->representante_nombre }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-5">
                                <div class="text-sm text-gray-700">{{ $reserva->recinto->nombre ?? 'N/A' }}</div>
                            </td>
                            
                            <td class="px-6 py-5">
                                <div class="text-sm">
                                    <div class="font-bold text-gray-900">{{ $reserva->fecha_reserva->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-5">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white text-gray-600 border border-gray-200">
                                    {{ $reserva->deporte }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-5 text-center">
                                <span class="text-sm font-bold text-gray-900">{{ $reserva->cantidad_personas }}</span>
                            </td>
                            
                            <td class="px-6 py-5 whitespace-nowrap">
                                @php
                                    $estadoConfig = [
                                        'pendiente' => ['text' => 'text-yellow-600'],
                                        'aprobada' => ['text' => 'text-green-600'],
                                        'rechazada' => ['text' => 'text-red-600']
                                    ];
                                    $config = $estadoConfig[$reserva->estado] ?? $estadoConfig['pendiente'];
                                @endphp
                                
                                <div class="space-y-1">
                                    <span class="text-xs font-bold uppercase tracking-wide {{ $config['text'] }}">
                                        {{ strtoupper($reserva->estado) }}
                                    </span>
                                    
                                    @if($reserva->fecha_cancelacion)
                                        <div>
                                            <span class="text-xs text-gray-400 italic">
                                                Cancelada
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-4">
                                    <a href="{{ route('admin.reservas.show', $reserva) }}" 
                                       class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors"
                                       title="Ver detalles">
                                        Ver
                                    </a>

                                    @if($reserva->puedeReportarIncidencia() && !$reserva->fecha_cancelacion)
                                        <a href="{{ route('admin.incidencias.crear', $reserva->id) }}" 
                                           class="text-sm font-medium text-orange-500 hover:text-orange-700 transition-colors"
                                           title="Reportar incidencia">
                                            Incidencia
                                            @if($reserva->tieneIncidencias())
                                                <span class="ml-1 text-orange-600 font-bold">
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

                <!-- Paginación Minimalista -->
                <div class="px-6 py-4 border-t border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-400">
                            Mostrando <span class="font-bold text-gray-700">{{ $reservas->firstItem() ?? 0 }}</span> a <span class="font-bold text-gray-700">{{ $reservas->lastItem() ?? 0 }}</span> 
                            de <span class="font-bold text-gray-700">{{ $reservas->total() }}</span> reservas
                        </div>
                        
                        @if($reservas->hasPages())
                            <div class="flex items-center gap-4">
                                @if($reservas->onFirstPage())
                                    <span class="text-sm text-gray-300 cursor-not-allowed">Anterior</span>
                                @else
                                    <a href="{{ $reservas->appends(request()->query())->previousPageUrl() }}" class="text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">Anterior</a>
                                @endif
                                
                                <span class="text-sm font-bold text-gray-700">Página {{ $reservas->currentPage() }} de {{ $reservas->lastPage() }}</span>
                                
                                @if($reservas->hasMorePages())
                                    <a href="{{ $reservas->appends(request()->query())->nextPageUrl() }}" class="text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">Siguiente</a>
                                @else
                                    <span class="text-sm text-gray-300 cursor-not-allowed">Siguiente</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="px-6 py-16 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-base font-medium text-gray-400 mt-4">No hay reservas que coincidan con los filtros</p>
                    <p class="text-sm text-gray-300 mt-1">Intenta cambiar los criterios de búsqueda</p>
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
<!-- SCRIPT PARA FILTROS EN TIEMPO REAL SIN RECARGAR PÁGINA -->
<script>
// ============================================
// FILTROS AJAX PARA DASHBOARD
// ============================================

let timeoutBusqueda = null;

// Función para cargar reservas con AJAX
function cargarReservasDashboard(pagina = 1) {
    const estado = document.getElementById('estado').value;
    const recintoId = document.getElementById('recinto_id').value;
    const deporte = document.getElementById('deporte').value;
    const fecha = document.getElementById('fecha').value;
    const buscarRut = document.getElementById('buscar_rut').value;
    const buscarOrg = document.getElementById('buscar_organizacion').value;

    // Mostrar indicador de carga
    const tablaContainer = document.querySelector('.overflow-x-auto');
    if (!document.getElementById('loading-dashboard')) {
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'loading-dashboard';
        loadingDiv.className = 'fixed top-4 right-4 z-50 bg-blue-50 border-l-4 border-blue-500 rounded-lg shadow-xl p-4';
        loadingDiv.innerHTML = `
            <div class="flex items-center">
                <svg class="animate-spin h-5 w-5 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-blue-700 font-medium">Buscando reservas...</span>
            </div>
        `;
        document.body.appendChild(loadingDiv);
    }
    
    tablaContainer.style.opacity = '0.5';

    // Construir URL con parámetros PARA FETCH (incluye ajax)
    const fetchParams = new URLSearchParams({
        estado: estado,
        recinto_id: recintoId,
        deporte: deporte,
        fecha: fecha,
        buscar_rut: buscarRut,
        buscar_organizacion: buscarOrg,
        page: pagina,
        ajax: '1'  // Solo para el fetch
    });

    // Construir URL para el historial (SIN ajax)
    const historyParams = new URLSearchParams({
        estado: estado,
        recinto_id: recintoId,
        deporte: deporte,
        fecha: fecha,
        buscar_rut: buscarRut,
        buscar_organizacion: buscarOrg,
        page: pagina
    });

    // Hacer petición AJAX
    fetch(`{{ route('admin.dashboard') }}?${fetchParams.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Ocultar indicador de carga
        const loadingEl = document.getElementById('loading-dashboard');
        if (loadingEl) loadingEl.remove();
        tablaContainer.style.opacity = '1';

        // Actualizar tabla
        const tbody = document.querySelector('tbody.divide-y.divide-gray-50');
        if (tbody) {
            tbody.innerHTML = data.html;
        }
        
        // Actualizar paginación y contador
        const paginacionContainer = document.querySelector('.px-6.py-4.border-t.border-gray-100');
        if (paginacionContainer && data.pagination) {
            const p = data.pagination;
            let paginacionHTML = '';
            
            // Botón Anterior (estilo minimalista)
            if (p.on_first_page) {
                paginacionHTML += '<span class="text-sm text-gray-300 cursor-not-allowed">Anterior</span>';
            } else {
                paginacionHTML += `<button type="button" onclick="cargarReservasDashboard(${p.current_page - 1})" class="text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">Anterior</button>`;
            }
            
            // Info de página
            paginacionHTML += `<span class="text-sm font-bold text-gray-700">Página ${p.current_page} de ${p.last_page}</span>`;
            
            // Botón Siguiente (estilo minimalista)
            if (p.has_more_pages) {
                paginacionHTML += `<button type="button" onclick="cargarReservasDashboard(${p.current_page + 1})" class="text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">Siguiente</button>`;
            } else {
                paginacionHTML += '<span class="text-sm text-gray-300 cursor-not-allowed">Siguiente</span>';
            }
            
            paginacionContainer.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-400">
                        Mostrando <span class="font-bold text-gray-700">${data.desde}</span> a <span class="font-bold text-gray-700">${data.hasta}</span> 
                        de <span class="font-bold text-gray-700">${data.total}</span> reservas
                    </div>
                    <div class="flex items-center gap-4">${paginacionHTML}</div>
                </div>
            `;
        }

        // Actualizar URL sin recargar - IMPORTANTE: sin el parámetro ajax
        const newUrl = `{{ route('admin.dashboard') }}?${historyParams.toString()}`;
        window.history.pushState({path: newUrl}, '', newUrl);
    })
    .catch(error => {
        console.error('Error:', error);
        const loadingEl = document.getElementById('loading-dashboard');
        if (loadingEl) loadingEl.remove();
        tablaContainer.style.opacity = '1';
        
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al cargar las reservas. Por favor, recarga la página.',
            confirmButtonColor: '#3b82f6'
        });
    });
}

// Esperar a que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    
    // Filtros inmediatos (select y date)
    const selectEstado = document.getElementById('estado');
    const selectRecinto = document.getElementById('recinto_id');
    const selectDeporte = document.getElementById('deporte');
    const inputFecha = document.getElementById('fecha');
    
    if (selectEstado) {
        selectEstado.addEventListener('change', () => cargarReservasDashboard());
    }
    if (selectRecinto) {
        selectRecinto.addEventListener('change', () => cargarReservasDashboard());
    }
    if (selectDeporte) {
        selectDeporte.addEventListener('change', () => cargarReservasDashboard());
    }
    if (inputFecha) {
        inputFecha.addEventListener('change', () => cargarReservasDashboard());
    }

    // Búsqueda por RUT con delay
    const inputRut = document.getElementById('buscar_rut');
    if (inputRut) {
        inputRut.addEventListener('input', function() {
            clearTimeout(timeoutBusqueda);
            timeoutBusqueda = setTimeout(() => {
                cargarReservasDashboard();
            }, 800); // Espera 800ms después de que el usuario deje de escribir
        });
    }

    // Búsqueda por Organización con delay
    const inputOrg = document.getElementById('buscar_organizacion');
    if (inputOrg) {
        inputOrg.addEventListener('input', function() {
            clearTimeout(timeoutBusqueda);
            timeoutBusqueda = setTimeout(() => {
                cargarReservasDashboard();
            }, 600); // Espera 600ms
        });
    }

    // Interceptar el submit del formulario para evitar recarga
    const form = document.querySelector('form[action="{{ route("admin.dashboard") }}"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            cargarReservasDashboard();
        });
    }

    // Manejar clics en paginación
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a[href*="page="]');
        
        if (link && link.closest('.px-6.py-4.border-t')) {
            e.preventDefault();
            const url = new URL(link.href);
            const page = url.searchParams.get('page');
            cargarReservasDashboard(page);
            
            // Scroll suave al inicio de la tabla
            const tablaContainer = document.querySelector('.overflow-x-auto');
            if (tablaContainer) {
                tablaContainer.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
        }
    });

    // Botón Limpiar
    const btnLimpiarForm = document.querySelector('a[href="{{ route("admin.dashboard") }}"]');
    if (btnLimpiarForm) {
        btnLimpiarForm.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Limpiar todos los campos
            if (selectEstado) selectEstado.value = '';
            if (selectRecinto) selectRecinto.value = '';
            if (selectDeporte) selectDeporte.value = '';
            if (inputFecha) inputFecha.value = '';
            if (inputRut) inputRut.value = '';
            if (inputOrg) inputOrg.value = '';
            
            // Recargar sin filtros
            cargarReservasDashboard();
        });
    }

    // Detectar navegación con botón "atrás" y forzar recarga completa
    // Detectar cuando el usuario usa botón atrás/adelante (popstate)
    window.addEventListener('popstate', function(event) 
    {
        window.location.reload();
    });

    // También detectar con performance API
    if (performance.navigation.type === 2) {
        // El usuario llegó usando botón atrás/adelante
        window.location.reload();
    }
});
</script>
@endsection