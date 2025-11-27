@extends('layouts.app')

@section('title', 'Inicio - Sistema de Reservas Deportivas')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    <!-- Hero Section -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">
            Bienvenido al Sistema de Reservas Deportivas
        </h1>
        <p class="text-xl text-gray-600">
            Municipalidad de Arica - Descubre nuestros eventos y reserva tus espacios deportivos
        </p>
    </div>

    <!-- Carrusel de Eventos -->
    @if($eventos->count() > 0)
    <div class="mb-16">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Próximos Eventos</h2>
        
        <!-- Carrusel Container -->
        <div class="relative max-w-5xl mx-auto">
            <!-- Slides -->
            <div id="carousel" class="relative overflow-hidden rounded-xl shadow-2xl bg-white" style="height: 500px;">
                @foreach($eventos as $index => $evento)
                <div class="carousel-slide absolute w-full h-full transition-opacity duration-500 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}" data-slide="{{ $index }}">
                    <div class="relative h-full">
                        <!-- Imagen -->
                        <img src="{{ $evento->imagen_url ?? 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=800' }}" 
                             alt="{{ $evento->titulo }}" 
                             class="w-full h-full object-cover">
                        
                        <!-- Overlay oscuro -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                        
                        <!-- Contenido -->
                        <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                            <div class="max-w-3xl">
                                <h3 class="text-3xl font-bold mb-3">{{ $evento->titulo }}</h3>
                                <p class="text-lg mb-4 text-gray-200">{{ $evento->descripcion }}</p>
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-semibold">
                                            {{ $evento->fecha_evento ? $evento->fecha_evento->locale('es')->isoFormat('D [de] MMMM, YYYY') : 'Fecha por confirmar' }}
                                        </span>
                                    </div>
                                    @if($evento->enlace_externo)
                                    <a href="{{ $evento->enlace_externo }}" target="_blank" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                                        Más información
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Controles del Carrusel -->
            @if($eventos->count() > 1)
            <button onclick="prevSlide()" 
                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-3 rounded-full shadow-lg transition-all z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <button onclick="nextSlide()" 
                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-3 rounded-full shadow-lg transition-all z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2 z-10">
                @foreach($eventos as $index => $evento)
                <button onclick="goToSlide({{ $index }})" 
                        class="carousel-indicator w-3 h-3 rounded-full transition-all {{ $index === 0 ? 'bg-white w-8' : 'bg-white/50' }}" 
                        data-index="{{ $index }}">
                </button>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="mb-16 bg-blue-50 border border-blue-200 rounded-lg p-8 text-center">
        <svg class="w-16 h-16 mx-auto text-blue-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-xl font-semibold text-blue-800 mb-2">No hay eventos próximos</h3>
        <p class="text-blue-600">Pronto publicaremos nuevos eventos deportivos y actividades</p>
    </div>
    @endif

    <!-- Calendario Semanal -->
    <div class="mb-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Disponibilidad Próximos 7 Días</h2>
        
        <div class="bg-white rounded-lg shadow-md p-6">
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
                                    $diasCerrados = is_array($recinto->dias_cerrados) 
                                        ? $recinto->dias_cerrados 
                                        : ($recinto->dias_cerrados ? json_decode($recinto->dias_cerrados, true) : []);
                                    
                                    $esDiaCerrado = in_array(strtolower($fecha->format('l')), $diasCerrados ?? []);
                                    $tieneReservas = false; // Puedes implementar lógica para verificar reservas
                                @endphp
                                
                                <button onclick="verDisponibilidad('{{ $fechaString }}', {{ $recinto->id }}, '{{ $recinto->nombre }}')"
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
        
        <!-- Leyenda -->
        <div class="mt-6 flex justify-center items-center flex-wrap gap-6 text-sm">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-600 rounded mr-2"></div>
                <span class="text-gray-600">Día actual</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-100 border border-green-300 rounded mr-2"></div>
                <span class="text-gray-600">Disponible</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-red-100 border border-red-300 rounded mr-2"></div>
                <span class="text-gray-600">Ocupado</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-yellow-100 border border-yellow-300 rounded mr-2"></div>
                <span class="text-gray-600">Mantenimiento</span>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-xl p-8 text-white text-center">
        <h3 class="text-3xl font-bold mb-4">¿Listo para reservar?</h3>
        <p class="text-xl mb-6 text-blue-100">
            Consulta la disponibilidad completa de nuestros recintos deportivos y solicita tu reserva
        </p>
        <a href="{{ route('calendario') }}" 
           class="inline-block bg-white text-blue-600 hover:bg-blue-50 font-bold px-8 py-3 rounded-lg transition-colors text-lg">
            Ver Calendario Completo
        </a>
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
// Variables globales
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-slide');
const indicators = document.querySelectorAll('.carousel-indicator');
const totalSlides = slides.length;

// Carrusel
function showSlide(index) {
    slides.forEach(slide => {
        slide.classList.remove('opacity-100');
        slide.classList.add('opacity-0');
    });
    
    slides[index].classList.remove('opacity-0');
    slides[index].classList.add('opacity-100');
    
    indicators.forEach((indicator, i) => {
        if (i === index) {
            indicator.classList.remove('bg-white/50', 'w-3');
            indicator.classList.add('bg-white', 'w-8');
        } else {
            indicator.classList.remove('bg-white', 'w-8');
            indicator.classList.add('bg-white/50', 'w-3');
        }
    });
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    showSlide(currentSlide);
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    showSlide(currentSlide);
}

function goToSlide(index) {
    currentSlide = index;
    showSlide(currentSlide);
}

if (totalSlides > 1) {
    setInterval(nextSlide, 5000);
}

// Modal de disponibilidad
function verDisponibilidad(fecha, recintoId, recintoNombre) {
    // Mostrar modal
    document.getElementById('modalDisponibilidad').classList.remove('hidden');
    document.getElementById('modalLoading').classList.remove('hidden');
    document.getElementById('modalContent').classList.add('hidden');

    // Hacer petición AJAX
    fetch(`{{ route('api.disponibilidad') }}?recinto_id=${recintoId}&fecha=${fecha}`, {
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
        mostrarDisponibilidad(data, fecha, recintoId);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar la disponibilidad: ' + error.message);
        cerrarModal();
    });
}

function mostrarDisponibilidad(data, fechaOriginal, recintoId) {
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
        const disponibles = data.horarios.filter(f => f.disponible).length;
        const total = data.horarios.length;
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

    // Franjas horarias
    const franjasDiv = document.getElementById('franjasHorarias');
    franjasDiv.innerHTML = data.horarios.map(franja => {
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

    // Botón de reserva
    document.getElementById('btnReservar').href = `/reservas/crear/${recintoId}?fecha=${fechaOriginal}`;
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