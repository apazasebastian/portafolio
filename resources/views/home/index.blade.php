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
            <!-- Botón Anterior -->
            <button onclick="prevSlide()" 
                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-3 rounded-full shadow-lg transition-all z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <!-- Botón Siguiente -->
            <button onclick="nextSlide()" 
                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-3 rounded-full shadow-lg transition-all z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Indicadores -->
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
    <!-- Mensaje si no hay eventos -->
    <div class="mb-16 bg-blue-50 border border-blue-200 rounded-lg p-8 text-center">
        <svg class="w-16 h-16 mx-auto text-blue-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-xl font-semibold text-blue-800 mb-2">No hay eventos próximos</h3>
        <p class="text-blue-600">Pronto publicaremos nuevos eventos deportivos y actividades</p>
    </div>
    @endif

    <!-- Calendarios -->
    <div class="mb-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Calendario de Disponibilidad</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Mes Actual -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center">
                    {{ $mesActual->locale('es')->isoFormat('MMMM YYYY') }}
                </h3>
                
                <!-- Días de la semana -->
                <div class="grid grid-cols-7 gap-2 mb-2">
                    @foreach(['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $dia)
                    <div class="text-center font-semibold text-gray-600 text-sm py-2">
                        {{ $dia }}
                    </div>
                    @endforeach
                </div>

                <!-- Días del mes -->
                <div class="grid grid-cols-7 gap-2">
                    @foreach($diasMesActual as $dia)
                        @if($dia === null)
                            <div class="aspect-square"></div>
                        @else
                            @php
                                $esHoy = $dia->isToday();
                                $esPasado = $dia->isPast() && !$esHoy;
                            @endphp
                            <div class="aspect-square flex items-center justify-center rounded-lg border transition-colors
                                {{ $esHoy ? 'bg-blue-600 text-white font-bold border-blue-600' : '' }}
                                {{ $esPasado ? 'bg-gray-100 text-gray-400 border-gray-200' : 'bg-white hover:bg-blue-50 border-gray-300' }}">
                                {{ $dia->day }}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Mes Siguiente -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center">
                    {{ $mesSiguiente->locale('es')->isoFormat('MMMM YYYY') }}
                </h3>
                
                <!-- Días de la semana -->
                <div class="grid grid-cols-7 gap-2 mb-2">
                    @foreach(['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $dia)
                    <div class="text-center font-semibold text-gray-600 text-sm py-2">
                        {{ $dia }}
                    </div>
                    @endforeach
                </div>

                <!-- Días del mes -->
                <div class="grid grid-cols-7 gap-2">
                    @foreach($diasMesSiguiente as $dia)
                        @if($dia === null)
                            <div class="aspect-square"></div>
                        @else
                            <div class="aspect-square flex items-center justify-center rounded-lg border bg-white hover:bg-blue-50 border-gray-300 transition-colors">
                                {{ $dia->day }}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Leyenda -->
        <div class="mt-6 flex justify-center items-center space-x-6 text-sm">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-600 rounded mr-2"></div>
                <span class="text-gray-600">Día actual</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-gray-100 border border-gray-300 rounded mr-2"></div>
                <span class="text-gray-600">Días pasados</span>
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

<!-- JavaScript para el carrusel -->
<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-slide');
const indicators = document.querySelectorAll('.carousel-indicator');
const totalSlides = slides.length;

function showSlide(index) {
    // Ocultar todos los slides
    slides.forEach(slide => {
        slide.classList.remove('opacity-100');
        slide.classList.add('opacity-0');
    });
    
    // Mostrar el slide actual
    slides[index].classList.remove('opacity-0');
    slides[index].classList.add('opacity-100');
    
    // Actualizar indicadores
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

// Auto-avanzar cada 5 segundos
if (totalSlides > 1) {
    setInterval(nextSlide, 5000);
}
</script>
@endsection