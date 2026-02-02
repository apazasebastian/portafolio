@extends('layouts.app')

@section('title', 'Inicio - Sistema de Reservas Deportivas')

@section('content')
<!-- Carrusel de Eventos + ¿Cómo Reservar? -->
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-4">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-0 shadow-2xl overflow-hidden">
        <!-- Carrusel de Eventos (2/3 del ancho) -->
        @if($eventos->count() > 0)
        <div class="relative lg:col-span-2 overflow-hidden">
            <!-- Slides -->
            <div id="carousel" class="relative overflow-hidden bg-white" style="height: 400px;">
                @foreach($eventos as $index => $evento)
                <div class="carousel-slide absolute w-full h-full transition-opacity duration-500 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}" data-slide="{{ $index }}">
                    <div class="relative h-full">
                        @php
                            $imagenUrl = 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=1600';
                            
                            if ($evento->imagen_url) {
                                if (str_starts_with($evento->imagen_url, 'http')) {
                                    $imagenUrl = $evento->imagen_url;
                                }
                                elseif (str_starts_with($evento->imagen_url, 'storage/') || str_starts_with($evento->imagen_url, 'eventos/')) {
                                    $imagenUrl = asset('storage/' . str_replace('storage/', '', $evento->imagen_url));
                                }
                                elseif (str_starts_with($evento->imagen_url, 'images/') || str_starts_with($evento->imagen_url, 'public/')) {
                                    $imagenUrl = asset($evento->imagen_url);
                                }
                                else {
                                    $imagenUrl = asset('storage/' . $evento->imagen_url);
                                }
                            }
                        @endphp
                        
                        <img src="{{ $imagenUrl }}" 
                             alt="{{ $evento->titulo }}" 
                             class="w-full h-full object-cover"
                             onerror="this.src='https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=1600'">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                        
                        <div class="absolute bottom-0 left-0 right-0 p-8">
                            <div class="mb-3">
                                <span class="inline-block bg-orange-700 text-white text-xs font-bold px-3 py-1 uppercase tracking-wide">
                                    NOVEDADES
                                </span>
                            </div>
                            
                            <h2 class="text-3xl md:text-4xl font-bold text-white mb-3 leading-tight">
                                NOTICIAS Y EVENTOS
                            </h2>
                            
                            <p class="text-white text-sm md:text-base mb-4 leading-relaxed max-w-lg line-clamp-2">
                                {{ $evento->descripcion }}
                            </p>
                            
                            <div class="flex items-center gap-3 flex-wrap">
                                <div class="flex items-center text-white text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $evento->fecha_evento ? $evento->fecha_evento->locale('es')->isoFormat('D [de] MMMM, YYYY') : 'Fecha por confirmar' }}</span>
                                </div>
                                
                                @if($evento->enlace_externo)
                                <a href="{{ $evento->enlace_externo }}" target="_blank" 
                                   class="inline-block bg-white hover:bg-gray-100 text-gray-800 font-bold text-sm px-5 py-2 transition-colors shadow-lg uppercase tracking-wide">
                                    Más Información
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Controles del Carrusel -->
            @if($eventos->count() > 1)
            <button onclick="prevSlide()" 
                    aria-label="Anterior diapositiva"
                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-3 shadow-xl transition-all z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <button onclick="nextSlide()" 
                    aria-label="Siguiente diapositiva"
                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-3 shadow-xl transition-all z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2 z-10">
                @foreach($eventos as $index => $evento)
                <button onclick="goToSlide({{ $index }})" 
                        aria-label="Ir a la diapositiva {{ $index + 1 }}"
                        class="carousel-indicator h-2.5 transition-all {{ $index === 0 ? 'bg-white w-8' : 'bg-white/50 w-2.5' }}" 
                        data-index="{{ $index }}">
                </button>
                @endforeach
            </div>
            @endif
        </div>
        @else
        <div class="lg:col-span-2 bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center" style="height: 400px;">
            <div class="text-center p-8">
                <div class="mb-3">
                    <span class="inline-block bg-orange-700 text-white text-xs font-bold px-3 py-1 uppercase tracking-wide">NOVEDADES</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-3">NOTICIAS Y EVENTOS</h2>
                <p class="text-white text-sm">Actualmente estamos coordinando las próximas fechas y actividades.</p>
            </div>
        </div>
        @endif

        <!-- ¿Cómo Reservar? (1/3 del ancho - más angosto) -->
        <div class="relative bg-blue-700 overflow-hidden" style="height: 400px;">
            <button onclick="abrirModalComoReservar()" class="absolute inset-0 w-full h-full group cursor-pointer focus:outline-none">
                <img src="{{ asset('images/municipalidad.jpg') }}" 
                     alt="Cómo Reservar" 
                     class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                     onerror="this.style.display='none'">
                
                <div class="absolute inset-0 bg-blue-700/80 group-hover:bg-blue-700/70 transition-colors"></div>
                
                <div class="absolute inset-0 flex flex-col items-center justify-center text-white p-4">
                    <div class="mb-4">
                        <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm group-hover:bg-white/30 transition-colors">
                            <svg class="w-10 h-10 text-white relative left-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M6.3 2.841A1.5 1.5 0 004 4.11v11.78a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <h3 class="text-xl md:text-2xl font-bold mb-2 text-center">
                        ¿CÓMO RESERVAR?
                    </h3>
                    
                    <p class="text-blue-100 text-xs uppercase tracking-wider border-t-2 border-white/30 pt-2 mt-1 text-center">
                        Video Instructivo
                    </p>
                </div>
            </button>
        </div>
    </div>
</div>

<!-- Sección de Recintos Deportivos -->
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-6">
    <div class="mb-8">
        <!-- Header más compacto -->
        <div class="bg-gradient-to-r from-blue-800 to-blue-900 text-white px-6 py-4 mb-0 shadow-lg">
            <div>
                <h2 class="text-xl md:text-2xl font-bold">NUESTROS RECINTOS DEPORTIVOS</h2>
                <p class="text-blue-100 text-xs mt-1">Conoce las instalaciones disponibles para la comunidad en Arica</p>
            </div>
        </div>

        <!-- Grid de Recintos -->
        <div class="bg-white border-x border-b border-gray-200 shadow-lg">
            <div class="p-6 lg:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    @foreach($recintos as $recinto)
                    @php
                        $datosRecintos = [
                            1 => ['direccion' => 'Pablo Picasso 2150', 'telefono' => '+56 58 260 9502', 'coords' => '-18.4783,-70.3126'],
                            2 => ['direccion' => 'Ginebra 3708', 'telefono' => '+56 58 228 6401', 'coords' => '-18.4856,-70.2987'],
                            3 => ['direccion' => 'Rafael Sotomayor 600', 'telefono' => '+56 58 238 6402', 'coords' => '-18.4742,-70.3148'],
                            4 => ['direccion' => 'Jr. España 2039', 'telefono' => '+56 58 238 4253', 'coords' => '-18.4697,-70.3213'],
                        ];
                        $info = $datosRecintos[$recinto->id] ?? ['direccion' => 'Arica', 'telefono' => 'No disponible', 'coords' => '-18.4783,-70.3126'];
                        $imagenUrl = $recinto->imagen_url ? asset('storage/' . $recinto->imagen_url) : 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800';
                    @endphp
                    
                    <div class="bg-white border border-gray-300 overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $imagenUrl }}" 
                                 alt="{{ $recinto->nombre }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                 onerror="this.src='https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800'">
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                            
                            <div class="absolute bottom-0 left-0 right-0 p-4">
                                <h3 class="text-white font-bold text-lg md:text-xl drop-shadow-lg uppercase tracking-wide">
                                    {{ $recinto->nombre }}
                                </h3>
                            </div>
                            
                            <div class="absolute top-3 right-3 bg-blue-600 text-white px-3 py-1.5 flex items-center gap-1.5 shadow-lg">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                                <span class="text-xs font-bold uppercase tracking-wide">Capacidad {{ $recinto->capacidad_maxima }}</span>
                            </div>
                        </div>
                        
                        <div class="p-5">
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3" style="min-height: 60px;">
                                {{ $recinto->descripcion ?: 'Gimnasio techado para múltiples disciplinas. Ideal para tenis y recreación familiar. Excelente iluminación.' }}
                            </p>
                            
                            <div class="space-y-2.5 text-sm mb-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mr-2 mt-0.5">
                                        <span class="font-bold text-gray-700 text-xs uppercase tracking-wide">Dirección:</span>
                                    </div>
                                    <span class="text-gray-600">{{ $info['direccion'] }}</span>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mr-2 mt-0.5">
                                        <span class="font-bold text-gray-700 text-xs uppercase tracking-wide">Teléfono:</span>
                                    </div>
                                    <span class="text-gray-600">{{ $info['telefono'] }}</span>
                                </div>
                            </div>
                            
                            <a href="{{ route('calendario') }}?recinto={{ $recinto->id }}" 
                               class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 transition-all shadow-md hover:shadow-lg uppercase tracking-wide text-sm">
                                Ver Disponibilidad
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN CONTACTO -->
    <div class="mb-4">
        <div class="bg-black overflow-hidden shadow-2xl">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <!-- Formulario de Contacto -->
                <div class="p-6 lg:p-10 bg-gradient-to-br from-gray-900 to-black">
                    <h2 class="text-3xl font-bold text-white mb-2 uppercase tracking-wide">CONTÁCTANOS</h2>
                    <p class="text-gray-400 mb-8 text-sm">¿Tienes alguna consulta sobre nuestros recintos? Escríbenos</p>
                    
                    <form id="formContacto" class="space-y-5">
                        @csrf
                        <div>
                            <label for="selectRecinto" class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wide">Recinto de interés</label>
                            <select id="selectRecinto" name="recinto_id" 
                                    class="w-full bg-gray-800/80 border-0 text-white px-4 py-3 focus:ring-2 focus:ring-orange-500 transition-all">
                                <option value="">Selecciona un recinto...</option>
                                @foreach($recintos as $recinto)
                                <option value="{{ $recinto->id }}" 
                                        data-email="{{ $encargadoEmails[$recinto->id] ?? 'reservas@muniarica.cl' }}"
                                        data-direccion="{{ $direccionesRecintos[$recinto->id] ?? '' }}">
                                    {{ $recinto->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="emailEncargado" class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wide">Email del encargado</label>
                            <input type="email" id="emailEncargado" name="email_encargado" readonly
                                   class="w-full bg-gray-800/50 border-0 text-gray-500 px-4 py-3 cursor-not-allowed"
                                   placeholder="Se llenará automáticamente...">
                        </div>
                        
                        <div>
                            <label for="nombre" class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wide">Nombre y Apellido</label>
                            <input type="text" id="nombre" name="nombre" required
                                   class="w-full bg-gray-800/80 border-0 text-white px-4 py-3 focus:ring-2 focus:ring-orange-500 transition-all placeholder-gray-600"
                                   placeholder="Ingresa tu nombre completo">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="email" class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wide">Tu Email</label>
                                <input type="email" id="email" name="email" required
                                       class="w-full bg-gray-800/80 border-0 text-white px-4 py-3 focus:ring-2 focus:ring-orange-500 transition-all placeholder-gray-600"
                                       placeholder="tucorreo@ejemplo.com">
                            </div>
                            
                            <div>
                                <label for="telefono" class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wide">Teléfono</label>
                                <input type="tel" id="telefono" name="telefono"
                                       class="w-full bg-gray-800/80 border-0 text-white px-4 py-3 focus:ring-2 focus:ring-orange-500 transition-all placeholder-gray-600"
                                       placeholder="+56 9 1234 5678">
                            </div>
                        </div>
                        
                        <div>
                            <label for="mensaje" class="block text-gray-400 text-xs font-semibold mb-2 uppercase tracking-wide">Mensaje</label>
                            <textarea id="mensaje" name="mensaje" rows="4" required
                                      class="w-full bg-gray-800/80 border-0 text-white px-4 py-3 focus:ring-2 focus:ring-orange-500 transition-all placeholder-gray-600 resize-none"
                                      placeholder="Cuéntanos tu consulta..."></textarea>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-orange-700 hover:bg-orange-800 text-white font-bold py-4 px-6 transition-all uppercase tracking-wider text-sm">
                            ▶ ENVIAR MENSAJE
                        </button>
                    </form>
                    
                    <div class="mt-8 pt-6 border-t border-gray-800 flex items-center justify-start gap-8 text-sm">
                        <div class="flex items-center text-gray-400">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                            </svg>
                            <span>+56 58 220 5500</span>
                        </div>
                        <div class="flex items-center text-gray-400">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                            <span>reservas@muniarica.cl</span>
                        </div>
                    </div>
                </div>
                
                <!-- Mapa y Ubicaciones -->
                <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-6 lg:p-10 flex flex-col">
                    <h3 class="text-3xl font-bold text-white mb-6 uppercase tracking-wide">UBICACIÓN DE RECINTOS</h3>
                    
                    <div class="space-y-3 mb-4">
                        <button onclick="cambiarMapa(1, 'Epicentro 1', 'Pablo Picasso 2150, Arica, Chile')" 
                                class="w-full text-left bg-gray-700/50 hover:bg-gray-700 border-0 p-4 transition-all group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-white group-hover:text-orange-400 transition-colors uppercase text-sm tracking-wide">EPICENTRO 1</p>
                                    <p class="text-xs text-gray-400 mt-1">Pablo Picasso 2150</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-500 group-hover:text-orange-500 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                        <button onclick="cambiarMapa(2, 'Epicentro 2', 'Ginebra 3708, Arica, Chile')" 
                                class="w-full text-left bg-gray-700/50 hover:bg-gray-700 border-0 p-4 transition-all group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-white group-hover:text-orange-400 transition-colors uppercase text-sm tracking-wide">EPICENTRO 2</p>
                                    <p class="text-xs text-gray-400 mt-1">Ginebra 3708</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-500 group-hover:text-orange-500 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                        <button onclick="cambiarMapa(3, 'Fortín Sotomayor', 'Rafael Sotomayor 600, Arica, Chile')" 
                                class="w-full text-left bg-gray-700/50 hover:bg-gray-700 border-0 p-4 transition-all group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-white group-hover:text-orange-400 transition-colors uppercase text-sm tracking-wide">FORTÍN SOTOMAYOR</p>
                                    <p class="text-xs text-gray-400 mt-1">Rafael Sotomayor 600</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-500 group-hover:text-orange-500 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                        <button onclick="cambiarMapa(4, 'Piscina Olímpica', 'España 121, Arica, Chile')" 
                                class="w-full text-left bg-gray-700/50 hover:bg-gray-700 border-0 p-4 transition-all group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-white group-hover:text-orange-400 transition-colors uppercase text-sm tracking-wide">PISCINA OLÍMPICA</p>
                                    <p class="text-xs text-gray-400 mt-1">España 121</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-500 group-hover:text-orange-500 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                    </div>
                    
                    <div class="flex-grow overflow-hidden border-0 min-h-[300px] bg-gray-800/30">
                        <iframe id="mapaRecinto"
                                title="Mapa de ubicación del recinto deportivo"
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3768.1!2d-70.3126!3d-18.4783!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTjCsDI4JzQyLjAiUyA3MMKwMTgnNDUuNCJX!5e0!3m2!1ses!2scl!4v1"
                                class="w-full h-full min-h-[300px]"
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                    
                    <div class="mt-4 bg-gray-900/80 border border-gray-700 p-3 text-center">
                        <p class="text-orange-400 font-semibold text-xs uppercase tracking-wider flex items-center justify-center" id="recintoMapaLabel">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            SELECCIONA UN RECINTO PARA VER SU UBICACIÓN
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Video -->
<div id="modalComoReservar" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="bg-white shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold">¿Cómo Reservar?</h3>
                <p class="text-blue-100 text-sm">Video instructivo</p>
            </div>
            <button onclick="cerrarModalComoReservar()" class="text-white hover:text-gray-200 transition-colors p-1" aria-label="Cerrar video">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <div class="aspect-video bg-black">
            <iframe id="videoComoReservar" title="Video instructivo sobre cómo reservar" class="w-full h-full" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
</div>

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
            indicator.classList.remove('bg-white/50', 'w-2.5');
            indicator.classList.add('bg-white', 'w-8');
        } else {
            indicator.classList.remove('bg-white', 'w-8');
            indicator.classList.add('bg-white/50', 'w-2.5');
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

// Video modal
const videoComoReservarUrl = 'https://www.youtube.com/embed/wX5_zY9S1WQ?autoplay=1&rel=0';

function abrirModalComoReservar() {
    document.getElementById('videoComoReservar').src = videoComoReservarUrl;
    document.getElementById('modalComoReservar').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function cerrarModalComoReservar() {
    document.getElementById('videoComoReservar').src = '';
    document.getElementById('modalComoReservar').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

document.getElementById('modalComoReservar')?.addEventListener('click', function(e) {
    if (e.target === this) cerrarModalComoReservar();
});

// Auto-rellenar email del encargado y actualizar mapa
document.getElementById('selectRecinto')?.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const email = selectedOption.dataset.email || '';
    const direccion = selectedOption.dataset.direccion || '';
    const nombreRecinto = selectedOption.text;
    
    // Actualizar email
    document.getElementById('emailEncargado').value = email;
    
    // Actualizar mapa si hay dirección
    if (direccion && this.value) {
        const mapaIframe = document.getElementById('mapaRecinto');
        const label = document.getElementById('recintoMapaLabel');
        
        // Usar mismo formato que el calendario
        const direccionEncoded = encodeURIComponent(direccion);
        const mapUrl = `https://www.google.com/maps?q=${direccionEncoded}&output=embed&z=16`;
        
        mapaIframe.src = mapUrl;
        label.innerHTML = `
            <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
            </svg>
            Mostrando: <strong>${nombreRecinto}</strong>
        `;
    }
});

// Cambiar mapa (botones de la izquierda - ahora direccion en vez de coords)
function cambiarMapa(recintoId, nombreRecinto, direccion) {
    const mapaIframe = document.getElementById('mapaRecinto');
    const label = document.getElementById('recintoMapaLabel');
    
    // Usar mismo formato que el calendario
    const direccionEncoded = encodeURIComponent(direccion);
    const mapUrl = `https://www.google.com/maps?q=${direccionEncoded}&output=embed&z=16`;
    
    mapaIframe.src = mapUrl;
    label.innerHTML = `
        <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
        </svg>
        Mostrando: <strong>${nombreRecinto}</strong>
    `;
    
    document.getElementById('selectRecinto').value = recintoId;
    document.getElementById('selectRecinto').dispatchEvent(new Event('change'));
}

// Manejo del formulario de contacto
document.getElementById('formContacto')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    const formData = new FormData(this);
    
    // Mostrar estado de carga
    btn.innerHTML = `
        <svg class="w-5 h-5 animate-spin inline-block" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Enviando...
    `;
    btn.disabled = true;
    
    // Enviar formulario via fetch
    fetch('{{ route('contacto.enviar') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito
            btn.innerHTML = `
                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                ¡Mensaje Enviado!
            `;
            btn.classList.remove('bg-orange-500', 'hover:bg-orange-600');
            btn.classList.add('bg-green-500', 'hover:bg-green-600');
            
            // Resetear formulario después de 2 segundos
            setTimeout(() => {
                this.reset();
                document.getElementById('emailEncargado').value = '';
                btn.innerHTML = originalText;
                btn.disabled = false;
                btn.classList.remove('bg-green-500', 'hover:bg-green-600');
                btn.classList.add('bg-orange-500', 'hover:bg-orange-600');
            }, 2000);
        } else {
            // Mostrar mensaje de error
            btn.innerHTML = `
                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Error al enviar
            `;
            btn.classList.remove('bg-orange-500', 'hover:bg-orange-600');
            btn.classList.add('bg-red-500', 'hover:bg-red-600');
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                btn.classList.remove('bg-red-500', 'hover:bg-red-600');
                btn.classList.add('bg-orange-500', 'hover:bg-orange-600');
            }, 3000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Mostrar mensaje de error
        btn.innerHTML = `
            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Error de conexión
        `;
        btn.classList.remove('bg-orange-500', 'hover:bg-orange-600');
        btn.classList.add('bg-red-500', 'hover:bg-red-600');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('bg-red-500', 'hover:bg-red-600');
            btn.classList.add('bg-orange-500', 'hover:bg-orange-600');
        }, 3000);
    });
});
</script>
@endsection
