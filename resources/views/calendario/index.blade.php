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
        $horarios = is_array($recinto->horarios_disponibles) 
            ? $recinto->horarios_disponibles 
            : json_decode($recinto->horarios_disponibles, true);
        
        $diasCerrados = is_array($recinto->dias_cerrados) 
            ? $recinto->dias_cerrados 
            : ($recinto->dias_cerrados ? json_decode($recinto->dias_cerrados, true) : null);
        
        //  PROCESAR NUEVA ESTRUCTURA CON FECHAS ESPECÍFICAS 
        $diasCompletosArray = [];
        $rangosBloqueados = [];
        
        if (is_array($diasCerrados)) {
            if (isset($diasCerrados['dias_completos'])) {
                $diasCompletosArray = $diasCerrados['dias_completos'];
            } elseif (!isset($diasCerrados['rangos_bloqueados'])) {
                // Retrocompatibilidad
                $diasCompletosArray = $diasCerrados;
            }
            
            if (isset($diasCerrados['rangos_bloqueados'])) {
                $rangosBloqueados = $diasCerrados['rangos_bloqueados'];
            }
        }
        
        // Nombres de días en español
        $nombresDias = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo'
        ];
    @endphp
    
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $recinto->nombre }}</h3>
        <p class="text-gray-600 text-sm mb-4">{{ $recinto->descripcion }}</p>
        
        <div class="text-sm text-gray-500 mb-4 space-y-2">
            <p><strong>Capacidad:</strong> {{ $recinto->capacidad_maxima }} personas</p>
            <p><strong>Horario:</strong> {{ $horarios['inicio'] ?? '08:00' }} - {{ $horarios['fin'] ?? '23:00' }}</p>
            
            @if(count($diasCompletosArray) > 0 || count($rangosBloqueados) > 0)
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <p class="text-red-600 font-semibold mb-1">Cerrado:</p>
                    <ul class="ml-3 space-y-0.5 text-xs">
                        {{-- Días completos cerrados --}}
                        @foreach($diasCompletosArray as $dia)
                            <li class="flex items-start">
                                <span class="text-red-500 mr-1">•</span>
                                <span>{{ $nombresDias[$dia] ?? ucfirst($dia) }} (día completo)</span>
                            </li>
                        @endforeach
                        
                        {{--  BLOQUEOS CON FECHAS ESPECÍFICAS  --}}
                        @foreach($rangosBloqueados as $bloqueo)
                            <li class="flex items-start">
                                <span class="text-orange-500 mr-1">•</span>
                                <span>
                                    @if(isset($bloqueo['fecha']))
                                        {{-- Formato: 24/12/2025 12:00-23:00 --}}
                                        {{ \Carbon\Carbon::parse($bloqueo['fecha'])->format('d/m/Y') }}
                                        {{ $bloqueo['hora_inicio'] }}-{{ $bloqueo['hora_fin'] }}
                                    @else
                                        {{-- Retrocompatibilidad con formato antiguo (día de semana) --}}
                                        {{ $nombresDias[$bloqueo['dia']] ?? ucfirst($bloqueo['dia']) }} 
                                        {{ $bloqueo['hora_inicio'] }}-{{ $bloqueo['hora_fin'] }}
                                    @endif
                                    @if(!empty($bloqueo['motivo']))
                                        <span class="text-gray-500">({{ $bloqueo['motivo'] }})</span>
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        
        <a href="{{ route('reservas.create', $recinto) }}" 
           class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition-colors">
            Solicitar Reserva
        </a>
    </div>
    @endforeach
</div>

    <!-- Calendarios Mensuales -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Disponibilidad por Mes</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Mes Actual -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 text-center">
                    {{ now()->locale('es')->isoFormat('MMMM YYYY') }}
                </h3>
                
                <!-- Días de la semana -->
                <div class="grid grid-cols-7 gap-2 mb-2">
                    @foreach(['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $dia)
                    <div class="text-center font-semibold text-gray-600 text-sm py-2">
                        {{ $dia }}
                    </div>
                    @endforeach
                </div>

                <!-- Días del mes actual -->
                <div class="grid grid-cols-7 gap-2">
                    @php
                        $mesActual = now()->startOfMonth();
                        $ultimoDia = now()->endOfMonth();
                        $primerDia = $mesActual->copy()->startOfMonth();
                        $diaSemanaInicio = $primerDia->dayOfWeek;
                    @endphp
                    
                    {{-- Días vacíos al inicio --}}
                    @for($i = 0; $i < $diaSemanaInicio; $i++)
                        <div class="aspect-square"></div>
                    @endfor
                    
                    {{-- Días del mes --}}
                    @php
                        $diaActual = $primerDia->copy();
                    @endphp
                    
                    @while($diaActual->lte($ultimoDia))
                        @php
                            $esHoy = $diaActual->isToday();
                            $esPasado = $diaActual->isPast() && !$esHoy;
                            $fechaString = $diaActual->format('Y-m-d');
                        @endphp
                        <button onclick="verDisponibilidadMensual('{{ $fechaString }}')"
                            class="aspect-square flex items-center justify-center rounded-lg border transition-colors
                            {{ $esHoy ? 'bg-blue-600 text-white font-bold border-blue-600' : '' }}
                            {{ $esPasado ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-white hover:bg-blue-50 hover:border-blue-400 border-gray-300 cursor-pointer' }}"
                            {{ $esPasado ? 'disabled' : '' }}>
                            {{ $diaActual->day }}
                        </button>
                        @php
                            $diaActual->addDay();
                        @endphp
                    @endwhile
                </div>
            </div>

            <!-- Mes Siguiente -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 text-center">
                    {{ now()->addMonth()->locale('es')->isoFormat('MMMM YYYY') }}
                </h3>
                
                <!-- Días de la semana -->
                <div class="grid grid-cols-7 gap-2 mb-2">
                    @foreach(['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $dia)
                    <div class="text-center font-semibold text-gray-600 text-sm py-2">
                        {{ $dia }}
                    </div>
                    @endforeach
                </div>

                <!-- Días del mes siguiente -->
                <div class="grid grid-cols-7 gap-2">
                    @php
                        $mesSiguiente = now()->addMonth()->startOfMonth();
                        $ultimoDiaSiguiente = now()->addMonth()->endOfMonth();
                        $primerDiaSiguiente = $mesSiguiente->copy();
                        $diaSemanaInicioSiguiente = $primerDiaSiguiente->dayOfWeek;
                    @endphp
                    
                    {{-- Días vacíos al inicio --}}
                    @for($i = 0; $i < $diaSemanaInicioSiguiente; $i++)
                        <div class="aspect-square"></div>
                    @endfor
                    
                    {{-- Días del mes --}}
                    @php
                        $diaActualSiguiente = $primerDiaSiguiente->copy();
                    @endphp
                    
                    @while($diaActualSiguiente->lte($ultimoDiaSiguiente))
                        @php
                            $fechaStringSiguiente = $diaActualSiguiente->format('Y-m-d');
                        @endphp
                        <button onclick="verDisponibilidadMensual('{{ $fechaStringSiguiente }}')"
                            class="aspect-square flex items-center justify-center rounded-lg border bg-white hover:bg-blue-50 hover:border-blue-400 border-gray-300 transition-colors cursor-pointer">
                            {{ $diaActualSiguiente->day }}
                        </button>
                        @php
                            $diaActualSiguiente->addDay();
                        @endphp
                    @endwhile
                </div>
            </div>
        </div>

        <!-- Leyenda -->
        <div class="mt-6 flex justify-center items-center flex-wrap gap-6 text-sm">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-600 rounded mr-2"></div>
                <span class="text-gray-600">Día actual</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-gray-100 border border-gray-300 rounded mr-2"></div>
                <span class="text-gray-600">Días pasados</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-100 border border-green-300 rounded mr-2"></div>
                <span class="text-gray-600">Disponible</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-red-100 border border-red-300 rounded mr-2"></div>
                <span class="text-gray-600">Ocupado</span>
            </div>
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

<!-- Modal de Disponibilidad (selector de recinto) -->
<div id="modalSeleccionRecinto" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 rounded-t-xl">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-2xl font-bold mb-1">Selecciona un Recinto</h3>
                    <p class="text-blue-100" id="fechaSeleccionada">Fecha seleccionada</p>
                </div>
                <button onclick="cerrarModalRecinto()" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="p-6 space-y-3" id="listaRecintos">
            @foreach($recintos as $recinto)
            <button onclick="verDisponibilidadRecinto({{ $recinto->id }}, '{{ $recinto->nombre }}')"
                    class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-blue-50 border border-gray-200 hover:border-blue-400 rounded-lg transition-colors flex items-center space-x-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="font-medium text-gray-700">{{ $recinto->nombre }}</span>
            </button>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal de Disponibilidad -->
<div id="modalDisponibilidad" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 rounded-t-xl">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-2xl font-bold mb-1" id="modalRecintoNombre">Cargando...</h3>
                    <p class="text-blue-100" id="modalFecha">Cargando...</p>
                </div>
                <button onclick="cerrarModal()" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div id="modalLoading" class="p-8 text-center">
            <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600">Cargando disponibilidad...</p>
        </div>

        <!-- Content -->
        <div id="modalContent" class="hidden p-6">
            <!-- Estado General -->
            <div id="estadoGeneral" class="mb-6"></div>

            <!-- Franjas Horarias -->
            <div class="mb-4">
                <h4 class="text-lg font-bold text-gray-800 mb-3">Disponibilidad por Horario</h4>
                <div id="franjasHorarias" class="space-y-2"></div>
            </div>

            <!-- Botón de Reserva -->
            <div class="mt-6">
                <a id="btnReservar" href="#" 
                   class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                    Solicitar Reserva
                </a>
            </div>
        </div>

        <!-- Error State -->
        <div id="modalError" class="hidden p-6 text-center">
            <svg class="w-12 h-12 text-red-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-red-600 font-semibold mb-2">Error al cargar la disponibilidad</p>
            <p class="text-gray-600 text-sm mb-4" id="errorMessage"></p>
            <button onclick="cerrarModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
let fechaSeleccionadaGlobal = null;

function verDisponibilidadMensual(fecha) {
    fechaSeleccionadaGlobal = fecha;
    const fechaFormateada = new Date(fecha + 'T00:00:00').toLocaleDateString('es-ES', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    document.getElementById('fechaSeleccionada').textContent = fechaFormateada;
    document.getElementById('modalSeleccionRecinto').classList.remove('hidden');
}

function cerrarModalRecinto() {
    document.getElementById('modalSeleccionRecinto').classList.add('hidden');
}

function verDisponibilidadRecinto(recintoId, recintoNombre) {
    cerrarModalRecinto();
    
    // Mostrar modal de disponibilidad
    document.getElementById('modalDisponibilidad').classList.remove('hidden');
    document.getElementById('modalLoading').classList.remove('hidden');
    document.getElementById('modalContent').classList.add('hidden');
    document.getElementById('modalError').classList.add('hidden');

    // Hacer petición AJAX
    fetch(`/api/disponibilidad?recinto_id=${recintoId}&fecha=${fechaSeleccionadaGlobal}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Respuesta API:', data);
        //  CORREGIDO: Pasar los 3 parámetros correctamente 
        mostrarDisponibilidad(data, recintoId, fechaSeleccionadaGlobal);
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError('No se pudo cargar la disponibilidad. Por favor, intenta nuevamente.');
    });
}

function mostrarError(mensaje) {
    document.getElementById('modalLoading').classList.add('hidden');
    document.getElementById('modalContent').classList.add('hidden');
    document.getElementById('modalError').classList.remove('hidden');
    document.getElementById('errorMessage').textContent = mensaje;
}

function mostrarDisponibilidad(data, recintoId, fecha) {
    document.getElementById('modalLoading').classList.add('hidden');
    document.getElementById('modalContent').classList.remove('hidden');

    document.getElementById('modalRecintoNombre').textContent = data.recinto;
    document.getElementById('modalFecha').textContent = data.fecha;

    const estadoDiv = document.getElementById('estadoGeneral');
    estadoDiv.innerHTML = ''; // Limpiar contenido previo
    
    //  Mostrar bloqueos de horarios si existen 
    if (data.bloqueos_dia && data.bloqueos_dia.length > 0) {
        let bloqueosList = data.bloqueos_dia.map(b => 
            `<li class="text-orange-700">• ${b.hora_inicio} - ${b.hora_fin} ${b.motivo ? '(' + b.motivo + ')' : ''}</li>`
        ).join('');
        
        estadoDiv.innerHTML += `
            <div class="bg-orange-50 border-l-4 border-orange-400 p-4 rounded mb-4">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-orange-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-bold text-orange-800">Horarios Bloqueados:</p>
                        <ul class="text-sm mt-1">${bloqueosList}</ul>
                    </div>
                </div>
            </div>
        `;
    }
    
    if (data.cerrado) {
        estadoDiv.innerHTML += `
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-bold text-yellow-800">Recinto Cerrado (Día Completo)</p>
                        <p class="text-yellow-700 text-sm">${data.motivo_cierre}</p>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Solo mostrar estadísticas si NO está cerrado
    if (!data.cerrado) {
        const disponibles = data.horarios.filter(f => f.disponible).length;
        const total = data.horarios.length;
        const bloqueados = data.horarios.filter(f => f.bloqueada).length;
        const ocupados = data.horarios.filter(f => f.reserva).length;
        
        estadoDiv.innerHTML += `
            <div class="grid grid-cols-4 gap-3 text-center">
                <div class="bg-blue-50 p-3 rounded-lg">
                    <p class="text-xl font-bold text-blue-600">${data.horario_general.inicio} - ${data.horario_general.fin}</p>
                    <p class="text-xs text-gray-600">Horario</p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg">
                    <p class="text-xl font-bold text-green-600">${disponibles}</p>
                    <p class="text-xs text-gray-600">Disponibles</p>
                </div>
                <div class="bg-orange-50 p-3 rounded-lg">
                    <p class="text-xl font-bold text-orange-600">${bloqueados}</p>
                    <p class="text-xs text-gray-600">Bloqueados</p>
                </div>
                <div class="bg-red-50 p-3 rounded-lg">
                    <p class="text-xl font-bold text-red-600">${ocupados}</p>
                    <p class="text-xs text-gray-600">Ocupados</p>
                </div>
            </div>
        `;
    }

    const franjasDiv = document.getElementById('franjasHorarias');
    franjasDiv.innerHTML = data.horarios.map(franja => {
        let bgColor, textColor, icon, estadoHtml;
        
        //  Bloqueado > Cerrado > Ocupado > Disponible 
        if (franja.bloqueada) {
            bgColor = 'bg-orange-50 border-orange-200';
            textColor = 'text-orange-700';
            icon = '<svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
            estadoHtml = '<p class="text-sm ' + textColor + ' font-semibold"> Bloqueado: ' + (franja.motivo_bloqueo || 'No disponible') + '</p>';
        } else if (data.cerrado) {
            bgColor = 'bg-yellow-50 border-yellow-200';
            textColor = 'text-yellow-700';
            icon = '<svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>';
            estadoHtml = '<p class="text-sm ' + textColor + '">Cerrado por mantenimiento</p>';
        } else if (franja.disponible) {
            bgColor = 'bg-green-50 border-green-200';
            textColor = 'text-green-700';
            icon = '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            estadoHtml = '<p class="text-sm ' + textColor + '">Disponible</p>';
        } else {
            bgColor = 'bg-red-50 border-red-200';
            textColor = 'text-red-700';
            icon = '<svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            
            if (franja.reserva) {
                estadoHtml = `
                    <p class="text-sm ${textColor} font-semibold">Reservado por ${franja.reserva.nombre_organizacion}</p>
                    <p class="text-xs ${textColor} mt-1">
                        <span class="inline-flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                            </svg>
                            ${franja.reserva.deporte}
                        </span>
                    </p>
                `;
            } else {
                estadoHtml = '<p class="text-sm ' + textColor + '">No disponible</p>';
            }
        }

        return `
            <div class="${bgColor} border rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        ${icon}
                        <div>
                            <p class="font-bold ${textColor}">${franja.hora_inicio} - ${franja.hora_fin}</p>
                            ${estadoHtml}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    //  CORREGIDO: Usar el parámetro fecha en lugar de fechaOriginal 
    document.getElementById('btnReservar').href = `/reservas/crear/${recintoId}?fecha=${fecha}`;
}

function cerrarModal() {
    document.getElementById('modalDisponibilidad').classList.add('hidden');
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal();
        cerrarModalRecinto();
    }
});

// Cerrar modal al hacer clic fuera
document.getElementById('modalDisponibilidad')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});

document.getElementById('modalSeleccionRecinto')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalRecinto();
    }
});
</script>
@endsection
