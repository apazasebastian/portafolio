@extends('layouts.app')

@section('title', 'Inicio - Sistema de Reservas Deportivas')

@section('content')
<!-- Carrusel de Eventos - Ancho reducido -->
@if($eventos->count() > 0)
<div class="container mx-auto px-4 mb-4">
    <!-- Carrusel Container -->
    <div class="relative w-full rounded-lg overflow-hidden shadow-lg">
        <!-- Slides -->
        <div id="carousel" class="relative overflow-hidden bg-white" style="height: 380px;">
            @foreach($eventos as $index => $evento)
            <div class="carousel-slide absolute w-full h-full transition-opacity duration-500 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}" data-slide="{{ $index }}">
                <div class="relative h-full">
                    <!-- Imagen -->
                    @php
                        // Manejo inteligente de rutas de imágenes
                        $imagenUrl = 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=1600'; // Default
                        
                        if ($evento->imagen_url) {
                            // Si es una URL completa (http/https)
                            if (str_starts_with($evento->imagen_url, 'http')) {
                                $imagenUrl = $evento->imagen_url;
                            }
                            // Si comienza con 'storage/' o 'eventos/'
                            elseif (str_starts_with($evento->imagen_url, 'storage/') || str_starts_with($evento->imagen_url, 'eventos/')) {
                                $imagenUrl = asset('storage/' . str_replace('storage/', '', $evento->imagen_url));
                            }
                            // Si comienza con 'images/' o 'public/'
                            elseif (str_starts_with($evento->imagen_url, 'images/') || str_starts_with($evento->imagen_url, 'public/')) {
                                $imagenUrl = asset($evento->imagen_url);
                            }
                            // Cualquier otra ruta relativa
                            else {
                                // Intenta primero con storage
                                $imagenUrl = asset('storage/' . $evento->imagen_url);
                            }
                        }
                    @endphp
                    <img src="{{ $imagenUrl }}" 
                         alt="{{ $evento->titulo }}" 
                         class="w-full h-full object-cover"
                         onerror="this.src='https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=1600'">
                    
                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                    
                    <!-- Contenido -->
                    <div class="absolute bottom-0 left-0 right-0 p-6 lg:p-8">
                        <div class="max-w-3xl">
                            <h3 class="text-2xl md:text-3xl font-bold mb-2 text-white">{{ $evento->titulo }}</h3>
                            <p class="text-base mb-4 text-gray-200 line-clamp-2">{{ $evento->descripcion }}</p>
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center bg-white/10 backdrop-blur-sm px-4 py-2 rounded-lg">
                                    <svg class="w-4 h-4 mr-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-semibold text-white text-sm">
                                        {{ $evento->fecha_evento ? $evento->fecha_evento->locale('es')->isoFormat('D [de] MMMM, YYYY') : 'Fecha por confirmar' }}
                                    </span>
                                </div>
                                @if($evento->enlace_externo)
                                <a href="{{ $evento->enlace_externo }}" target="_blank" 
                                   class="bg-white hover:bg-gray-100 text-primary px-5 py-2 rounded-lg font-bold text-sm transition-colors shadow-lg">
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
                class="absolute left-6 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-4 rounded-full shadow-xl transition-all z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <button onclick="nextSlide()" 
                class="absolute right-6 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-4 rounded-full shadow-xl transition-all z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex space-x-2 z-10">
            @foreach($eventos as $index => $evento)
            <button onclick="goToSlide({{ $index }})" 
                    class="carousel-indicator h-3 rounded-full transition-all {{ $index === 0 ? 'bg-white w-10' : 'bg-white/50 w-3' }}" 
                    data-index="{{ $index }}">
            </button>
            @endforeach
        </div>
        @endif
    </div>
</div>
@else
<div class="w-full bg-gray-100 py-20 mb-12">
    <div class="container mx-auto px-4 text-center">
        <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-2xl font-semibold text-gray-800 mb-2">No hay eventos próximos</h3>
        <p class="text-gray-600">Pronto publicaremos nuevos eventos deportivos y actividades</p>
    </div>
</div>
@endif

<!-- Sección de Contenido Principal -->
<div class="container mx-auto px-4 py-4">
    <!-- Primera fila: Como Reservar (1/3) + Calendario (2/3) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-4">
        
        <!-- ¿Cómo Reservar? - Imagen Clickable con Modal (Columna estrecha) -->
        <div class="lg:col-span-4">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2 rounded-t-lg">
                <h2 class="text-lg font-bold flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    ¿Cómo Reservar?
                </h2>
            </div>
            <div class="bg-white border border-gray-200 rounded-b-lg shadow-md overflow-hidden">
                <!-- Imagen Clickable -->
                <button onclick="abrirModalComoReservar()" class="w-full relative group cursor-pointer focus:outline-none">
                    <img src="{{ asset('images/reserva-recinto.jpg') }}" 
                         alt="Reserva de Recinto" 
                         class="w-full h-48 lg:h-56 object-cover transition-transform duration-300 group-hover:scale-105">
                    <!-- Overlay con efecto hover -->
                    <div class="absolute inset-0 bg-black bg-opacity-40 group-hover:bg-opacity-50 transition-all duration-300 flex items-center justify-center">
                        <div class="text-center text-white">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-90" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-lg font-bold">Ver instrucciones</p>
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <!--  CALENDARIO SEMANAL MEJORADO (Columna ancha)  -->
        <div class="lg:col-span-8">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2 rounded-t-lg">
                <h2 class="text-lg font-bold flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                    Disponibilidad Próximos 7 Días
                </h2>
            </div>
            <div class="bg-white border border-gray-200 rounded-b-lg p-4 shadow-md">
                <!-- Grid de días (estilo mensual) -->
                <div class="grid grid-cols-7 gap-2">
                    @php
                        $diasEspanol = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                    @endphp
                    
                    @for($i = 0; $i < 7; $i++)
                        @php
                            $fecha = now()->addDays($i);
                            $fechaString = $fecha->format('Y-m-d');
                            $esHoy = $fecha->isToday();
                            $indiceDia = $fecha->dayOfWeek;
                            $nombreDia = $diasEspanol[$indiceDia];
                        @endphp
                        
                        <button onclick="verDisponibilidadSemanal('{{ $fechaString }}')"
                                class="aspect-square flex flex-col items-center justify-center rounded-lg border-2 transition-all hover:shadow-md
                                {{ $esHoy ? 'bg-blue-50 border-blue-600' : 'bg-white border-gray-300 hover:border-blue-400' }}">
                            <!-- Día de la semana -->
                            <div class="text-xs font-bold text-gray-500 uppercase mb-1">
                                {{ $nombreDia }}
                            </div>
                            <!-- Número del día -->
                            <div class="text-xl lg:text-2xl font-bold {{ $esHoy ? 'text-blue-600' : 'text-gray-800' }}">
                                {{ $fecha->format('d') }}
                            </div>
                            <!-- Mes -->
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $fecha->locale('es')->translatedFormat('M') }}
                            </div>
                        </button>
                    @endfor
                </div>
                
                <!-- Leyenda -->
                <div class="mt-4 pt-3 border-t border-gray-200">
                    <div class="grid grid-cols-4 gap-2 text-xs">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-50 border-2 border-green-300 rounded mr-1"></div>
                            <span class="text-gray-600">Disponible</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-orange-50 border-2 border-orange-300 rounded mr-1"></div>
                            <span class="text-gray-600">Bloqueado</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-50 border-2 border-red-300 rounded mr-1"></div>
                            <span class="text-gray-600">Ocupado</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-50 border-2 border-blue-600 rounded mr-1"></div>
                            <span class="text-gray-600">Hoy</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sección de Videos -->
    <div class="mb-4">
        <!-- Videos del Recinto -->
        <div>
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-t-lg">
                <h2 class="text-xl font-bold flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                    </svg>
                    Videos de los Recintos Deportivos
                </h2>
            </div>
            <div class="bg-white border border-gray-200 rounded-b-lg p-6 shadow-md">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Video Embed -->
                    <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden">
                        <iframe 
                            class="w-full h-full"
                            src="https://www.youtube.com/embed/wX5_zY9S1WQ" 
                            title="Video Recinto Deportivo"
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                            allowfullscreen>
                        </iframe>
                    </div>
                    <!-- Descripción -->
                    <div class="flex flex-col justify-center">
                        <h3 class="font-semibold text-gray-800 text-xl mb-3">Conoce Nuestros Recintos</h3>
                        <p class="text-gray-600 mb-4">Descubre las instalaciones deportivas que la Municipalidad de Arica pone a disposición de la comunidad.</p>
                        <a href="{{ route('reglamentos') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                            </svg>
                            Ver Reglamentos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal ¿Cómo Reservar? -->
<div id="modalComoReservar" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 rounded-t-xl">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-2xl font-bold mb-1">¿Cómo Reservar?</h3>
                    <p class="text-blue-100">Sigue estos sencillos pasos</p>
                </div>
                <button onclick="cerrarModalComoReservar()" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="space-y-5">
                <div class="flex items-start">
                    <div class="bg-blue-100 text-blue-600 rounded-full w-10 h-10 flex items-center justify-center font-bold mr-4 flex-shrink-0 text-lg">1</div>
                    <div>
                        <h4 class="font-semibold text-gray-800 text-lg">Selecciona una fecha</h4>
                        <p class="text-gray-600">Elige el día en el calendario semanal o mensual</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="bg-blue-100 text-blue-600 rounded-full w-10 h-10 flex items-center justify-center font-bold mr-4 flex-shrink-0 text-lg">2</div>
                    <div>
                        <h4 class="font-semibold text-gray-800 text-lg">Elige el recinto</h4>
                        <p class="text-gray-600">Selecciona el recinto deportivo disponible</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="bg-blue-100 text-blue-600 rounded-full w-10 h-10 flex items-center justify-center font-bold mr-4 flex-shrink-0 text-lg">3</div>
                    <div>
                        <h4 class="font-semibold text-gray-800 text-lg">Completa el formulario</h4>
                        <p class="text-gray-600">Ingresa tus datos y el horario deseado</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="bg-green-100 text-green-600 rounded-full w-10 h-10 flex items-center justify-center font-bold mr-4 flex-shrink-0 text-lg">✓</div>
                    <div>
                        <h4 class="font-semibold text-gray-800 text-lg">Espera confirmación</h4>
                        <p class="text-gray-600">Recibirás un correo cuando tu reserva sea aprobada</p>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('calendario') }}" 
               class="mt-8 block w-full text-center bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 px-6 rounded-lg transition-all shadow-lg hover:shadow-xl text-lg">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Comenzar Reserva
            </a>
        </div>
    </div>
</div>

<!-- Modal de Selección de Recinto -->
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
// Variables globales
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-slide');
const indicators = document.querySelectorAll('.carousel-indicator');
const totalSlides = slides.length;
let fechaSeleccionadaGlobal = null;

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
            indicator.classList.add('bg-white', 'w-10');
        } else {
            indicator.classList.remove('bg-white', 'w-10');
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

//  NUEVAS FUNCIONES ESTILO CALENDARIO MENSUAL 

function verDisponibilidadSemanal(fecha) {
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

// Funciones para modal ¿Cómo Reservar?
function abrirModalComoReservar() {
    document.getElementById('modalComoReservar').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function cerrarModalComoReservar() {
    document.getElementById('modalComoReservar').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Cerrar modal al hacer click fuera
document.getElementById('modalComoReservar')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalComoReservar();
    }
});

function verDisponibilidadRecinto(recintoId, recintoNombre) {
    cerrarModalRecinto();
    
    // Mostrar modal de disponibilidad
    document.getElementById('modalDisponibilidad').classList.remove('hidden');
    document.getElementById('modalLoading').classList.remove('hidden');
    document.getElementById('modalContent').classList.add('hidden');
    document.getElementById('modalError').classList.add('hidden');

    // Hacer petición AJAX
    fetch(`{{ route('api.disponibilidad') }}?recinto_id=${recintoId}&fecha=${fechaSeleccionadaGlobal}`, {
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
    estadoDiv.innerHTML = '';
    
    // Mostrar bloqueos de horarios si existen
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