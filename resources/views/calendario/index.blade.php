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

    <!-- Calendario Semanal -->
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
                                
                                $diasCerradosCalendario = is_array($recinto->dias_cerrados) 
                                    ? $recinto->dias_cerrados 
                                    : ($recinto->dias_cerrados ? json_decode($recinto->dias_cerrados, true) : null);
                                
                                $esDiaCerrado = false;
                                if ($diasCerradosCalendario && is_array($diasCerradosCalendario)) {
                                    $esDiaCerrado = in_array(strtolower($fecha->format('l')), $diasCerradosCalendario);
                                }
                            @endphp
                            
                            <button onclick="verDisponibilidadRecinto({{ $recinto->id }}, '{{ $recinto->nombre }}', '{{ $fechaString }}')"
                                class="w-full text-xs mb-1 p-2 rounded transition-all hover:shadow-md {{ 
                                $esDiaCerrado ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 
                                ($tieneReservas ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200') 
                            }}">
                                <div class="font-medium">{{ Str::limit($recinto->nombre, 15) }}</div>
                                @if($esDiaCerrado)
                                    <span class="block text-xs mt-1">🔒 Cerrado</span>
                                @elseif($tieneReservas)
                                    <span class="block text-xs mt-1">📅 Ocupado</span>
                                @else
                                    <span class="block text-xs mt-1">✅ Disponible</span>
                                @endif
                            </button>
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
    </div>
</div>

<!-- JavaScript -->
<script>
function verDisponibilidadRecinto(recintoId, recintoNombre, fecha) {
    // Mostrar modal
    document.getElementById('modalDisponibilidad').classList.remove('hidden');
    document.getElementById('modalLoading').classList.remove('hidden');
    document.getElementById('modalContent').classList.add('hidden');

    // Hacer petición AJAX
    fetch(`/api/disponibilidad?recinto_id=${recintoId}&fecha=${fecha}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        mostrarDisponibilidad(data, recintoId);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar la disponibilidad');
        cerrarModal();
    });
}

function mostrarDisponibilidad(data, recintoId) {
    // Ocultar loading
    document.getElementById('modalLoading').classList.add('hidden');
    document.getElementById('modalContent').classList.remove('hidden');

    // Actualizar header
    document.getElementById('modalRecintoNombre').textContent = data.recinto;
    document.getElementById('modalFecha').textContent = data.fecha;

    // Estado general
    const estadoDiv = document.getElementById('estadoGeneral');
    if (data.cerrado) {
        estadoDiv.innerHTML = `
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-bold text-yellow-800">Recinto Cerrado</p>
                        <p class="text-yellow-700 text-sm">${data.motivo_cierre}</p>
                    </div>
                </div>
            </div>
        `;
    } else {
        const disponibles = data.franjas_horarias.filter(f => f.disponible).length;
        const total = data.franjas_horarias.length;
        estadoDiv.innerHTML = `
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600">${data.horario_general.inicio} - ${data.horario_general.fin}</p>
                    <p class="text-sm text-gray-600">Horario</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">${disponibles}/${total}</p>
                    <p class="text-sm text-gray-600">Franjas Disponibles</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <p class="text-2xl font-bold text-red-600">${data.total_reservas}</p>
                    <p class="text-sm text-gray-600">Reservas</p>
                </div>
            </div>
        `;
    }

    // Franjas horarias - SECCIÓN CORREGIDA
    const franjasDiv = document.getElementById('franjasHorarias');
    franjasDiv.innerHTML = data.franjas_horarias.map(franja => {
        let bgColor, textColor, icon, estadoHtml;
        
        if (data.cerrado) {
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
            
            // AQUÍ ESTÁ LA CORRECCIÓN PRINCIPAL - mostrar organización y deporte
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

    // Extraer solo la fecha del string completo
    const soloFecha = data.fecha.match(/\d{4}-\d{2}-\d{2}/);
    const fechaFormateada = soloFecha ? soloFecha[0] : new Date().toISOString().split('T')[0];
    
    // Botón de reserva
    document.getElementById('btnReservar').href = `/reservas/crear/${recintoId}?fecha=${fechaFormateada}`;
}

function cerrarModal() {
    document.getElementById('modalDisponibilidad').classList.add('hidden');
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal();
    }
});

// Cerrar modal al hacer clic fuera
document.getElementById('modalDisponibilidad').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});
</script>
@endsection