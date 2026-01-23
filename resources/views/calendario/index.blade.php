@extends('layouts.app')

@section('title', 'Calendario de Recintos Deportivos - Municipalidad de Arica')

@section('content')
@if($recintoSeleccionado)
{{-- Vista con recinto seleccionado --}}
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Columna Izquierda: Calendario (70%) --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 p-6">
                {{-- Header con navegación --}}
                <div class="flex items-center justify-between mb-6">
                    <button onclick="cambiarMes(-1)" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium transition-colors">
                        ANTERIOR
                    </button>
                    <h2 class="text-xl font-bold text-gray-800 uppercase" id="mesActualTitulo">
                        MES
                    </h2>
                    <button onclick="cambiarMes(1)" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium transition-colors">
                        SIGUIENTE
                    </button>
                </div>

                {{-- Días de la semana --}}
                <div class="grid grid-cols-7 gap-2 mb-2">
                    @foreach(['LUN', 'MAR', 'MIÉ', 'JUE', 'VIE', 'SÁB', 'DOM'] as $dia)
                    <div class="text-center font-semibold text-gray-600 text-xs py-2">
                        {{ $dia }}
                    </div>
                    @endforeach
                </div>

                {{-- Grid de días del mes --}}
                <div class="grid grid-cols-7 gap-2" id="calendarioGrid">
                    {{-- Se llenará con JavaScript --}}
                </div>
            </div>
        </div>

        {{-- Columna Derecha: Sidebar Información (30%) --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 sticky top-4">
                {{-- Header --}}
                <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                    <h3 class="text-xs font-bold text-gray-600 uppercase tracking-wide">RECINTO SELECCIONADO</h3>
                </div>

                {{-- Información del Recinto --}}
                <div class="p-4">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 uppercase">{{ $recintoSeleccionado->nombre }}</h2>
                    
                    @if($encargadoInfo && isset($encargadoInfo['direccion']))
                    <p class="text-sm text-gray-600 mb-6">{{ $encargadoInfo['direccion'] }}</p>
                    @endif

                    {{-- Booking Rules --}}
                    <div class="mb-6">
                        <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">REGLAS DE RESERVA</h4>
                        <p class="text-sm text-gray-800">MÁXIMO 2 HORAS POR SESIÓN. SE REQUIERE PERMISO MUNICIPAL PARA EVENTOS.</p>
                    </div>

                    {{-- Operating Hours --}}
                    <div class="mb-6">
                        <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">HORARIO DE ATENCIÓN</h4>
                        @php
                            $horarios = is_array($recintoSeleccionado->horarios_disponibles) 
                                ? $recintoSeleccionado->horarios_disponibles 
                                : json_decode($recintoSeleccionado->horarios_disponibles, true);
                            $horaInicio = $horarios['inicio'] ?? '08:00';
                            $horaFin = $horarios['fin'] ?? '23:00';
                        @endphp
                        <p class="text-sm text-gray-800">{{ $horaInicio }} - {{ $horaFin }}</p>
                    </div>

                    {{-- Manager Info --}}
                    @if($encargadoInfo)
                    <div class="mb-6">
                        <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">ENCARGADO</h4>
                        <p class="text-sm text-gray-800 font-medium">{{ $encargadoInfo['nombre'] }}</p>
                        <p class="text-sm text-gray-600">{{ $encargadoInfo['email'] }}</p>
                    </div>
                    @endif

                    {{-- Closed Days --}}
                    @php
                        $diasCerrados = is_array($recintoSeleccionado->dias_cerrados) 
                            ? $recintoSeleccionado->dias_cerrados 
                            : json_decode($recintoSeleccionado->dias_cerrados, true);
                        
                        $diasCompletos = [];
                        if (isset($diasCerrados['dias_completos']) && is_array($diasCerrados['dias_completos'])) {
                            $diasCompletos = $diasCerrados['dias_completos'];
                        } elseif (!isset($diasCerrados['dias_completos']) && !isset($diasCerrados['rangos_bloqueados'])) {
                            $diasCompletos = $diasCerrados;
                        }
                        
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
                    
                    @if(count($diasCompletos) > 0)
                    <div class="mb-6">
                        <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">DÍAS CERRADOS</h4>
                        @foreach($diasCompletos as $dia)
                            <p class="text-sm text-gray-800">{{ $nombresDias[$dia] ?? ucfirst($dia) }}</p>
                        @endforeach
                    </div>
                    @endif

                    {{-- Mapa Google Maps --}}
                    <div class="mt-8">
                        <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">UBICACIÓN</h4>
                        @php
                            // Direcciones de cada recinto para Google Maps
                            $direcciones = [
                                1 => 'Pablo Picasso 2150, Arica, Chile',  // Epicentro 1
                                2 => 'Ginebra 3708, Arica, Chile',        // Epicentro 2
                                3 => 'Rafael Sotomayor 600, Arica, Chile', // Fortín Sotomayor
                                4 => 'España 121, Arica, Chile',          // Piscina Olímpica
                            ];
                            
                            $direccion = $direcciones[$recintoSeleccionado->id] ?? 'Arica, Chile';
                            $direccionEncoded = urlencode($direccion);
                        @endphp
                        <div class="overflow-hidden border border-gray-300 h-48">
                            <iframe 
                                src="https://www.google.com/maps?q={{ $direccionEncoded }}&output=embed&z=16"
                                class="w-full h-full"
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@else
{{-- Vista sin recinto seleccionado: mostrar mensaje --}}
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-8">
    <div class="bg-white border border-gray-200 p-12 text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Selecciona un Recinto</h2>
        <p class="text-gray-600 mb-8">Para ver el calendario, selecciona un recinto desde la página de inicio.</p>
        <a href="{{ route('home') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium px-8 py-3 transition-colors">
            IR A INICIO
        </a>
    </div>
</div>
@endif

{{-- Modal de Selección de Horarios --}}
<div id="modalHorarios" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="sticky top-0 bg-blue-600 text-white p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-2xl font-bold mb-1">SELECCIONAR HORARIO</h3>
                    <p class="text-blue-100 text-sm" id="fechaModalSeleccionada">Selecciona los horarios para tu reserva</p>
                </div>
                <button onclick="cerrarModalHorarios()" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Loading --}}
        <div id="horariosLoading" class="p-8 text-center">
            <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600">Cargando horarios disponibles...</p>
        </div>

        {{-- Content --}}
        <div id="horariosContent" class="hidden p-6">
            {{-- Mensaje de error continuidad --}}
            <div id="mensajeErrorContinuidad" class="hidden bg-red-50 border border-red-200 p-4 mb-4">
                <p class="text-red-800 text-sm font-medium">Los horarios seleccionado deben ser continuos</p>
            </div>

            {{-- Lista de bloques --}}
            <div id="listaBloques" class="space-y-2 mb-6">
                {{-- Se llenará con JavaScript --}}
            </div>

            {{-- Botón Ir al Formulario --}}
            <button id="btnIrFormulario" onclick="irAlFormulario()" disabled
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 transition-all disabled:bg-gray-300 disabled:cursor-not-allowed uppercase">
                IR AL FORMULARIO
            </button>
        </div>
    </div>
</div>

<script>
// Variables globales
@if($recintoSeleccionado)
    const RECINTO_ID = {{ $recintoSeleccionado->id }};
@endif
let mesActual = new Date();
let bloquesSeleccionados = [];
let todosBloquesDisponibles = [];
let fechaSeleccionadaGlobal = null;

// Al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    @if($recintoSeleccionado)
        cargarCalendario();
    @endif
});

// Cargar calendario del mes actual
function cargarCalendario() {
    const año = mesActual.getFullYear();
    const mes = mesActual.getMonth();
    
    // Actualizar título
    const nombreMes = mesActual.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
    document.getElementById('mesActualTitulo').textContent = nombreMes.toUpperCase();
    
    // Calcular primer día del mes
    const primerDia = new Date(año, mes, 1);
    const ultimoDia = new Date(año, mes + 1, 0);
    
    // Calcular día de la semana del primer día (0=Domingo, 1=Lunes, etc.)
    let diaSemanaInicio = primerDia.getDay();
    // Ajustar para que Lunes sea 0
    diaSemanaInicio = diaSemanaInicio === 0 ? 6 : diaSemanaInicio - 1;
    
    const grid = document.getElementById('calendarioGrid');
    grid.innerHTML = '';
    
    // Días vacíos al inicio
    for (let i = 0; i < diaSemanaInicio; i++) {
        const divVacio = document.createElement('div');
        divVacio.className = 'aspect-square';
        grid.appendChild(divVacio);
    }
    
    // Días del mes
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    
    for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
        const fecha = new Date(año, mes, dia);
        fecha.setHours(0, 0, 0, 0);
        const fechaString = fecha.toISOString().split('T')[0];
        const esPasado = fecha <= hoy;
        
        const btn = document.createElement('button');
        btn.className = 'aspect-square border border-gray-300 flex flex-col items-center justify-center p-2 transition-colors';
        btn.innerHTML = `<span class="text-lg font-bold text-gray-800">${String(dia).padStart(2, '0')}</span><span class="text-xs font-medium mt-1 loading-estado" data-fecha="${fechaString}">...</span>`;
        
        if (esPasado) {
            btn.className += ' bg-gray-100 cursor-not-allowed';
            btn.disabled = true;
        } else {
            btn.className += ' bg-white hover:bg-gray-50 cursor-pointer';
            btn.onclick = () => abrirDia(fechaString);
        }
        
        grid.appendChild(btn);
        
        // Cargar estado del día si no es pasado
        if (!esPasado) {
            cargarEstadoDia(fechaString);
        }
    }
}

// Cargar estado de un día
async function cargarEstadoDia(fecha) {
    try {
        const response = await fetch(`/api/estado-dia?recinto_id=${RECINTO_ID}&fecha=${fecha}`);
        const data = await response.json();
        
        const elementos = document.querySelectorAll(`[data-fecha="${fecha}"]`);
        elementos.forEach(el => {
            let clase = '';
            let texto = data.estado;
            
            if (data.estado === 'DISPONIBLE') {
                clase = 'text-green-600';
            } else if (data.estado === 'OCUPADO') {
                clase = 'text-red-600';
            } else if (data.estado === 'MANTENIMIENTO') {
                clase = 'text-orange-600';
            }
            
            el.textContent = texto;
            el.className = `text-xs font-medium mt-1 ${clase}`;
            
            // Si está ocupado o en mantenimiento, deshabilitar el botón
            if (data.estado !== 'DISPONIBLE') {
                const btn = el.closest('button');
                if (btn) {
                    btn.disabled = true;
                    btn.className = btn.className.replace('hover:bg-gray-50 cursor-pointer', 'cursor-not-allowed');
                    btn.onclick = null;
                }
            }
        });
    } catch (error) {
        console.error('Error cargando estado del día:', error);
    }
}

// Cambiar mes
function cambiarMes(direccion) {
    mesActual.setMonth(mesActual.getMonth() + direccion);
    cargarCalendario();
}

// Abrir modal de horarios para un día
async function abrirDia(fecha) {
    fechaSeleccionadaGlobal = fecha;
    bloquesSeleccionados = [];
    
    // Formatear fecha para mostrar
    const fechaObj = new Date(fecha + 'T00:00:00');
    const fechaFormateada = fechaObj.toLocaleDateString('es-ES', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    document.getElementById('fechaModalSeleccionada').textContent = fechaFormateada;
    
    // Mostrar modal
    document.getElementById('modalHorarios').classList.remove('hidden');
    document.getElementById('horariosLoading').classList.remove('hidden');
    document.getElementById('horariosContent').classList.add('hidden');
    document.getElementById('mensajeErrorContinuidad').classList.add('hidden');
    
    // Cargar horarios
    try {
        const response = await fetch(`/api/disponibilidad?recinto_id=${RECINTO_ID}&fecha=${fecha}`);
        const data = await response.json();
        
        // Filtrar solo horarios disponibles
        todosBloquesDisponibles = data.horarios.filter(h => h.disponible);
        
        mostrarHorarios(todosBloquesDisponibles);
    } catch (error) {
        console.error('Error cargando horarios:', error);
        alert('Error al cargar los horarios. Intenta nuevamente.');
        cerrarModalHorarios();
    }
}

// Mostrar lista de horarios disponibles
function mostrarHorarios(horarios) {
    document.getElementById('horariosLoading').classList.add('hidden');
    document.getElementById('horariosContent').classList.remove('hidden');
    
    const lista = document.getElementById('listaBloques');
    lista.innerHTML = '';
    
    if (horarios.length === 0) {
        lista.innerHTML = '<p class="text-center text-gray-600 py-4">No hay horarios disponibles para este día</p>';
        return;
    }
    
    horarios.forEach((bloque, index) => {
        const div = document.createElement('div');
        div.className = 'border border-gray-300 p-4 flex items-center cursor-pointer hover:bg-gray-50 transition-colors bloque-horario';
        div.dataset.index = index;
        div.onclick = () => toggleBloque(index);
        
        div.innerHTML = `
            <input type="checkbox" class="w-5 h-5 text-blue-600" id="bloque_${index}">
            <label for="bloque_${index}" class="ml-3 text-gray-800 font-medium cursor-pointer">${bloque.hora_inicio} - ${bloque.hora_fin}</label>
        `;
        
        lista.appendChild(div);
    });
}

// Toggle selección de bloque
function toggleBloque(index) {
    const checkbox = document.getElementById(`bloque_${index}`);
    const estaSeleccionado = bloquesSeleccionados.includes(index);
    
    if (estaSeleccionado) {
        // Deseleccionar
        bloquesSeleccionados= bloquesSeleccionados.filter(i => i !== index);
        checkbox.checked = false;
    } else {
        // Intentar seleccionar
        const nuevaSeleccion = [...bloquesSeleccionados, index].sort((a, b) => a - b);
        
        // Validar continuidad
        if (validarContinuidad(nuevaSeleccion)) {
            bloquesSeleccionados = nuevaSeleccion;
            checkbox.checked = true;
            document.getElementById('mensajeErrorContinuidad').classList.add('hidden');
        } else {
            // Mostrar error y NO marcar el checkbox
            checkbox.checked = false;
            document.getElementById('mensajeErrorContinuidad').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('mensajeErrorContinuidad').classList.add('hidden');
            }, 3000);
        }
    }
    
    actualizarBotonFormulario();
}

// Validar que los bloques sean continuos
function validarContinuidad(indices) {
    if (indices.length <= 1) return true;
    
    for (let i = 1; i < indices.length; i++) {
        if (indices[i] !== indices[i-1] + 1) {
            return false;
        }
    }
    
    return true;
}

// Actualizar estado del botón
function actualizarBotonFormulario() {
    const btn = document.getElementById('btnIrFormulario');
    btn.disabled = bloquesSeleccionados.length === 0;
}

// Ir al formulario con los bloques seleccionados
function irAlFormulario() {
    if (bloquesSeleccionados.length === 0) return;
    
    // Construir array de bloques horarios
    const bloquesData = bloquesSeleccionados.map(i => {
        const bloque = todosBloquesDisponibles[i];
        return `${bloque.hora_inicio}-${bloque.hora_fin}`;
    });
    
    // Redirigir al formulario
    const url = `/reservas/crear/${RECINTO_ID}?fecha=${fechaSeleccionadaGlobal}&bloques=${bloquesData.join(',')}`;
    window.location.href = url;
}

// Cerrar modal
function cerrarModalHorarios() {
    document.getElementById('modalHorarios').classList.add('hidden');
    bloquesSeleccionados = [];
}

// Cerrar con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalHorarios();
    }
});
</script>
@endsection
