@extends('layouts.app')

@section('title', 'Inicio - Sistema de Reservas Deportivas')

@section('content')
<!-- Carrusel de Eventos + ¿Cómo Reservar? - Layout 50/50 -->
<div class="max-w-7xl mx-auto px-6 lg:px-8 mb-4">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Carrusel de Eventos (50%) -->
        @if($eventos->count() > 0)
        <div class="relative w-full overflow-hidden shadow-lg">
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
                        <div class="absolute bottom-0 left-0 right-0 p-6">
                            <div class="max-w-xl">
                                <h3 class="text-xl md:text-2xl font-bold mb-2 text-white">{{ $evento->titulo }}</h3>
                                <p class="text-sm mb-3 text-gray-200 line-clamp-2">{{ $evento->descripcion }}</p>
                                <div class="flex items-center space-x-2 flex-wrap gap-2">
                                    <div class="flex items-center bg-white/10 backdrop-blur-sm px-3 py-1.5">
                                        <svg class="w-4 h-4 mr-1 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-semibold text-white text-xs">
                                            {{ $evento->fecha_evento ? $evento->fecha_evento->locale('es')->isoFormat('D [de] MMMM, YYYY') : 'Fecha por confirmar' }}
                                        </span>
                                    </div>
                                    @if($evento->enlace_externo)
                                    <a href="{{ $evento->enlace_externo }}" target="_blank" 
                                       class="bg-white hover:bg-gray-100 text-primary px-4 py-1.5 font-bold text-xs transition-colors shadow-lg">
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
                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-3 shadow-xl transition-all z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <button onclick="nextSlide()" 
                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-3 shadow-xl transition-all z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2 z-10">
                @foreach($eventos as $index => $evento)
                <button onclick="goToSlide({{ $index }})" 
                        class="carousel-indicator h-2.5 transition-all {{ $index === 0 ? 'bg-white w-8' : 'bg-white/50 w-2.5' }}" 
                        data-index="{{ $index }}">
                </button>
                @endforeach
            </div>
            @endif
        </div>
        @else
        <div class="w-full bg-gray-100 flex items-center justify-center" style="height: 380px;">
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No hay eventos próximos</h3>
                <p class="text-gray-600 text-sm">Pronto publicaremos nuevos eventos</p>
            </div>
        </div>
        @endif

        <!-- ¿Cómo Reservar? (50%) -->
        <div class="overflow-hidden shadow-lg" style="height: 380px;">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-3">
                <h2 class="text-xl font-bold flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    ¿Cómo Reservar?
                </h2>
            </div>
            <button onclick="abrirModalComoReservar()" class="w-full relative group cursor-pointer focus:outline-none" style="height: calc(380px - 52px);">
                <img src="{{ asset('images/reserva-recinto.jpg') }}" 
                     alt="Reserva de Recinto" 
                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                <!-- Overlay con efecto hover -->
                <div class="absolute inset-0 bg-black bg-opacity-40 group-hover:bg-opacity-50 transition-all duration-300 flex items-center justify-center">
                    <div class="text-center text-white">
                        <svg class="w-20 h-20 mx-auto mb-3 opacity-90" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xl font-bold">Ver video instructivo</p>
                        <p class="text-sm text-gray-200 mt-1">Aprende cómo hacer tu reserva</p>
                    </div>
                </div>
            </button>
        </div>
    </div>
</div>

<!-- ==================== SECCIÓN NUESTROS RECINTOS ==================== -->
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-4">
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-800 to-blue-900 text-white px-6 py-4-lg">
            <h2 class="text-2xl font-bold flex items-center">
                <svg class="w-7 h-7 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-.707 1.707L10 19.414l-5.293-1.707A1 1 0 014 16V4z" clip-rule="evenodd"/>
                </svg>
                Nuestros Recintos Deportivos
            </h2>
            <p class="text-blue-200 mt-1">Conoce las instalaciones disponibles para la comunidad</p>
        </div>
        <div class="bg-gradient-to-br from-slate-50 to-blue-50 border border-gray-200-lg p-6 shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                @foreach($recintos as $recinto)
                @php
                    // Datos de contacto por recinto
                    $datosRecintos = [
                        1 => ['direccion' => 'Pablo Picasso 2150, Arica', 'telefono' => '(58) 260 9502', 'coords' => '-18.4783,-70.3126'],
                        2 => ['direccion' => 'Ginebra 3708, Arica', 'telefono' => '(58) 220 0000', 'coords' => '-18.4856,-70.2987'],
                        3 => ['direccion' => 'Rafael Sotomayor 600, Arica', 'telefono' => '(58) 220 6582', 'coords' => '-18.4742,-70.3148'],
                        4 => ['direccion' => 'España 121, Arica', 'telefono' => '(58) 220 0001', 'coords' => '-18.4697,-70.3213'],
                    ];
                    $info = $datosRecintos[$recinto->id] ?? ['direccion' => 'Arica', 'telefono' => 'No disponible', 'coords' => '-18.4783,-70.3126'];
                    
                    // Imagen del recinto
                    $imagenUrl = $recinto->imagen_url 
                        ? asset('storage/' . $recinto->imagen_url) 
                        : 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800';
                @endphp
                <div class="bg-white shadow-lg overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <!-- Imagen del recinto -->
                    <div class="relative h-48 overflow-hidden">
                        <img src="{{ $imagenUrl }}" 
                             alt="{{ $recinto->nombre }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                             onerror="this.src='https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800'">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute bottom-3 left-3 right-3">
                            <h3 class="text-white font-bold text-lg drop-shadow-lg">{{ $recinto->nombre }}</h3>
                        </div>
                        <!-- Badge de capacidad -->
                        <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1">
                            <span class="text-sm font-bold text-blue-800">
                                <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                                {{ $recinto->capacidad_maxima }}
                            </span>
                        </div>
                    </div>
                    <!-- Contenido -->
                    <div class="p-4">
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $recinto->descripcion }}</p>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center text-gray-500">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="truncate">{{ $info['direccion'] }}</span>
                            </div>
                            <div class="flex items-center text-gray-500">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                </svg>
                                <span>{{ $info['telefono'] }}</span>
                            </div>
                        </div>
                        <!-- Botón Ver Disponibilidad -->
                        <a href="{{ route('calendario') }}?recinto={{ $recinto->id }}" 
                           class="mt-4 block w-full text-center bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2 px-4 transition-all shadow-md hover:shadow-lg">
                            Ver Disponibilidad
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>


    <!-- ==================== SECCIÓN CONTACTO ==================== -->
    <div class="mb-4">
        <div class="bg-black overflow-hidden shadow-2xl">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <!-- Formulario de Contacto -->
                <div class="p-6 lg:p-8">
                    <h2 class="text-2xl font-bold text-white mb-2">Contáctanos</h2>
                    <p class="text-gray-400 mb-6">¿Tienes alguna consulta sobre nuestros recintos? Escríbenos</p>
                    
                    <form id="formContacto" class="space-y-4">
                        @csrf
                        <!-- Selector de Recinto -->
                        <div>
                            <label class="block text-gray-300 text-sm font-medium mb-2">Recinto de interés</label>
                            <select id="selectRecinto" name="recinto_id" 
                                    class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-2.5 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                                <option value="">Selecciona un recinto...</option>
                                @foreach($recintos as $recinto)
                                @php
                                    $encargadoEmails = [
                                        1 => 'carlosapazac33@gmail.com',
                                        2 => 'gomezchurabrayan@gmail.com',
                                        3 => 'reservas@muniarica.cl',
                                        4 => 'apazasebastian@gmail.com',
                                    ];
                                @endphp
                                <option value="{{ $recinto->id }}" data-email="{{ $encargadoEmails[$recinto->id] ?? 'reservas@muniarica.cl' }}">
                                    {{ $recinto->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Email del encargado (auto-rellenado) -->
                        <div>
                            <label class="block text-gray-300 text-sm font-medium mb-2">Email del encargado</label>
                            <input type="email" id="emailEncargado" name="email_encargado" readonly
                                   class="w-full bg-gray-800 border border-gray-700 text-gray-400 px-4 py-2.5 cursor-not-allowed"
                                   placeholder="Se llenará automáticamente...">
                        </div>
                        
                        <!-- Nombre -->
                        <div>
                            <label class="block text-gray-300 text-sm font-medium mb-2">Nombre y Apellido</label>
                            <input type="text" name="nombre" required
                                   class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-2.5 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all placeholder-gray-500"
                                   placeholder="Ingresa tu nombre completo">
                        </div>
                        
                        <!-- Email del usuario -->
                        <div>
                            <label class="block text-gray-300 text-sm font-medium mb-2">Tu Email</label>
                            <input type="email" name="email" required
                                   class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-2.5 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all placeholder-gray-500"
                                   placeholder="tucorreo@ejemplo.com">
                        </div>
                        
                        <!-- Teléfono -->
                        <div>
                            <label class="block text-gray-300 text-sm font-medium mb-2">Teléfono</label>
                            <input type="tel" name="telefono"
                                   class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-2.5 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all placeholder-gray-500"
                                   placeholder="+56 9 1234 5678">
                        </div>
                        
                        <!-- Mensaje -->
                        <div>
                            <label class="block text-gray-300 text-sm font-medium mb-2">Mensaje</label>
                            <textarea name="mensaje" rows="3" required
                                      class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-2.5 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all placeholder-gray-500 resize-none"
                                      placeholder="Cuéntanos tu consulta..."></textarea>
                        </div>
                        
                        <!-- Botón Enviar -->
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold py-3 px-6 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            ENVIAR MENSAJE
                        </button>
                    </form>
                    
                    <!-- Info de contacto adicional -->
                    <div class="mt-6 pt-5 border-t border-gray-800">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div class="flex items-center text-gray-400">
                                <svg class="w-5 h-5 mr-3 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                </svg>
                                <span>+56 58 220 5500</span>
                            </div>
                            <div class="flex items-center text-gray-400">
                                <svg class="w-5 h-5 mr-3 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                                <span>reservas@muniarica.cl</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mapa y Ubicaciones -->
                <div class="bg-gray-900 p-6 lg:p-8 flex flex-col">
                    <h3 class="text-xl font-bold text-white mb-3 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        Ubicación de Recintos
                    </h3>
                    
                    <!-- Lista de ubicaciones -->
                    <div class="space-y-2 mb-4">
                        <button onclick="cambiarMapa(1, 'Epicentro 1', '-18.4783,-70.3126')" 
                                class="w-full text-left bg-gray-800 hover:bg-gray-700 border border-gray-700 hover:border-orange-500 p-2.5 transition-all group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-white group-hover:text-orange-400 transition-colors">Epicentro 1</p>
                                    <p class="text-sm text-gray-400">Pablo Picasso 2150</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-500 group-hover:text-orange-500 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                        <button onclick="cambiarMapa(2, 'Epicentro 2', '-18.4856,-70.2987')" 
                                class="w-full text-left bg-gray-800 hover:bg-gray-700 border border-gray-700 hover:border-orange-500 p-2.5 transition-all group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-white group-hover:text-orange-400 transition-colors">Epicentro 2</p>
                                    <p class="text-sm text-gray-400">Ginebra 3708</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-500 group-hover:text-orange-500 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                        <button onclick="cambiarMapa(3, 'Fortín Sotomayor', '-18.4742,-70.3148')" 
                                class="w-full text-left bg-gray-800 hover:bg-gray-700 border border-gray-700 hover:border-orange-500 p-2.5 transition-all group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-white group-hover:text-orange-400 transition-colors">Fortín Sotomayor</p>
                                    <p class="text-sm text-gray-400">Rafael Sotomayor 600</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-500 group-hover:text-orange-500 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                        <button onclick="cambiarMapa(4, 'Piscina Olímpica', '-18.4697,-70.3213')" 
                                class="w-full text-left bg-gray-800 hover:bg-gray-700 border border-gray-700 hover:border-orange-500 p-2.5 transition-all group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-white group-hover:text-orange-400 transition-colors">Piscina Olímpica</p>
                                    <p class="text-sm text-gray-400">España 121</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-500 group-hover:text-orange-500 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                    </div>
                    
                    <!-- Mapa Google Maps Embed -->
                    <div class="flex-grow overflow-hidden border border-gray-700 min-h-[280px]">
                        <iframe id="mapaRecinto"
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3768.1!2d-70.3126!3d-18.4783!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTjCsDI4JzQyLjAiUyA3MMKwMTgnNDUuNCJX!5e0!3m2!1ses!2scl!4v1"
                                class="w-full h-full min-h-[280px]"
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                    
                    <!-- Indicador del recinto seleccionado -->
                    <div class="mt-3 bg-orange-500/20 border border-orange-500/40 p-2.5 text-center">
                        <p class="text-orange-400 font-medium" id="recintoMapaLabel">
                            <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            Selecciona un recinto para ver su ubicación
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal ¿Cómo Reservar? - Video Instructivo -->
<div id="modalComoReservar" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="bg-white shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold">¿Cómo Reservar?</h3>
                <p class="text-blue-100 text-sm">Video instructivo</p>
            </div>
            <button onclick="cerrarModalComoReservar()" class="text-white hover:text-gray-200 transition-colors p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <div class="aspect-video bg-black">
            <iframe id="videoComoReservar"
                    class="w-full h-full"
                    src="" 
                    title="Cómo reservar - Video instructivo"
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    allowfullscreen>
            </iframe>
        </div>
    </div>
</div>

<!-- Modal de Selección de Recinto -->
<div id="modalSeleccionRecinto" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6-xl">
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
                    class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-blue-50 border border-gray-200 hover:border-blue-400 transition-colors flex items-center space-x-3">
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
    <div class="bg-white shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6-xl">
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
                   class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 transition-colors">
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
            <button onclick="cerrarModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4">
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

// Funciones para modal ¿Cómo Reservar? con Video
// URL del video de YouTube (cambiar según necesites)
const videoComoReservarUrl = 'https://www.youtube.com/embed/wX5_zY9S1WQ?autoplay=1&rel=0';

function abrirModalComoReservar() {
    const modal = document.getElementById('modalComoReservar');
    const videoIframe = document.getElementById('videoComoReservar');
    
    // Establecer la URL del video con autoplay
    videoIframe.src = videoComoReservarUrl;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function cerrarModalComoReservar() {
    const modal = document.getElementById('modalComoReservar');
    const videoIframe = document.getElementById('videoComoReservar');
    
    // Detener el video al cerrar
    videoIframe.src = '';
    
    modal.classList.add('hidden');
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
            <div class="bg-orange-50 border-l-4 border-orange-400 p-4 mb-4">
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
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
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
                <div class="bg-blue-50 p-3">
                    <p class="text-xl font-bold text-blue-600">${data.horario_general.inicio} - ${data.horario_general.fin}</p>
                    <p class="text-xs text-gray-600">Horario</p>
                </div>
                <div class="bg-green-50 p-3">
                    <p class="text-xl font-bold text-green-600">${disponibles}</p>
                    <p class="text-xs text-gray-600">Disponibles</p>
                </div>
                <div class="bg-orange-50 p-3">
                    <p class="text-xl font-bold text-orange-600">${bloqueados}</p>
                    <p class="text-xs text-gray-600">Bloqueados</p>
                </div>
                <div class="bg-red-50 p-3">
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
            <div class="${bgColor} border p-4">
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

// Auto-rellenar email del encargado al seleccionar recinto
document.getElementById('selectRecinto')?.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const email = selectedOption.dataset.email || '';
    document.getElementById('emailEncargado').value = email;
});

// Cambiar mapa según recinto seleccionado
function cambiarMapa(recintoId, nombreRecinto, coords) {
    const [lat, lng] = coords.split(',');
    const mapaIframe = document.getElementById('mapaRecinto');
    const label = document.getElementById('recintoMapaLabel');
    
    // Construir URL del mapa con las coordenadas
    const mapUrl = `https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1500!2d${lng}!3d${lat}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2z${encodeURIComponent(nombreRecinto)}!5e0!3m2!1ses!2scl!4v1`;
    
    mapaIframe.src = mapUrl;
    label.innerHTML = `
        <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
        </svg>
        Mostrando: <strong>${nombreRecinto}</strong>
    `;
    
    // También actualizar el selector del formulario
    document.getElementById('selectRecinto').value = recintoId;
    document.getElementById('selectRecinto').dispatchEvent(new Event('change'));
}

// Manejo del formulario de contacto (estructura sin envío)
document.getElementById('formContacto')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Por ahora solo mostrar confirmación visual
    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    
    btn.innerHTML = `
        <svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Enviando...
    `;
    btn.disabled = true;
    
    // Simular envío (después puedes conectar con backend)
    setTimeout(() => {
        btn.innerHTML = `
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            ¡Mensaje Enviado!
        `;
        btn.classList.remove('from-orange-500', 'to-orange-600');
        btn.classList.add('from-green-500', 'to-green-600');
        
        // Restaurar después de 3 segundos
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('from-green-500', 'to-green-600');
            btn.classList.add('from-orange-500', 'to-orange-600');
            this.reset();
            document.getElementById('emailEncargado').value = '';
        }, 3000);
    }, 1500);
});
</script>
@endsection