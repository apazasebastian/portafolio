<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Reservas Deportivas')</title>
    
    <!-- Favicon de la municipalidad -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e3a8a', // Azul oscuro institucional
                        secondary: '#3b82f6', // Azul medio
                        accent: '#f59e0b', // Naranja/amarillo para acentos
                    }
                }
            }
        }
    </script>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/js/app.js'])
    
    <style>
        /* Fuente más profesional */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Animaciones suaves */
        .nav-link {
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: #1e3a8a; /* primary color */
        }

        /* Elementos decorativos de fondo */
        .bg-decorativo {
            position: fixed;
            pointer-events: none;
            z-index: 0;
        }

        .bg-decorativo-left {
            left: -50px;
            top: 0;
            width: 250px;
            height: 100vh;
        }

        .bg-decorativo-right {
            right: -50px;
            top: 0;
            width: 250px;
            height: 100vh;
        }

        /* Contenedor principal con z-index */
        .contenedor-principal {
            position: relative;
            z-index: 1;
        }

        /* Animación de las formas */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        @keyframes floatReverse {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(20px) rotate(-5deg); }
        }

        .forma-flotante-1 {
            animation: float 8s ease-in-out infinite;
        }

        .forma-flotante-2 {
            animation: floatReverse 10s ease-in-out infinite;
            animation-delay: 1s;
        }

        .forma-flotante-3 {
            animation: float 12s ease-in-out infinite;
            animation-delay: 2s;
        }

        .forma-flotante-4 {
            animation: floatReverse 9s ease-in-out infinite;
            animation-delay: 1.5s;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Decoraciones de fondo - Lado izquierdo -->
    <div class="bg-decorativo bg-decorativo-left hidden lg:block">
        <svg viewBox="0 0 250 1000" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Forma azul grande superior -->
            <path class="forma-flotante-1" d="M-20 100 Q80 80 120 180 T100 320 Q50 380 -10 330 Z" fill="#0ea5e9" opacity="0.15"/>
            
            <!-- Círculo naranja pequeño -->
            <circle class="forma-flotante-2" cx="60" cy="250" r="45" fill="#f97316" opacity="0.2"/>
            
            <!-- Forma rosa media -->
            <ellipse class="forma-flotante-3" cx="40" cy="450" rx="70" ry="90" fill="#ec4899" opacity="0.12"/>
            
            <!-- Círculo amarillo -->
            <circle class="forma-flotante-1" cx="100" cy="600" r="50" fill="#eab308" opacity="0.18"/>
            
            <!-- Forma azul inferior -->
            <path class="forma-flotante-4" d="M10 750 L90 800 L70 900 L-10 870 Z" fill="#3b82f6" opacity="0.15"/>
            
            <!-- Línea decorativa curva -->
            <path d="M0 200 Q80 250 120 200" stroke="#0ea5e9" stroke-width="4" opacity="0.15" fill="none"/>
            
            <!-- Círculo pequeño violeta -->
            <circle class="forma-flotante-2" cx="80" cy="380" r="30" fill="#8b5cf6" opacity="0.15"/>
        </svg>
    </div>

    <!-- Decoraciones de fondo - Lado derecho -->
    <div class="bg-decorativo bg-decorativo-right hidden lg:block">
        <svg viewBox="0 0 250 1000" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Forma rosa grande -->
            <path class="forma-flotante-2" d="M180 150 Q240 120 270 220 T230 380 Q180 430 140 380 Z" fill="#ec4899" opacity="0.15"/>
            
            <!-- Círculo naranja grande -->
            <circle class="forma-flotante-1" cx="200" cy="300" r="80" fill="#f97316" opacity="0.18"/>
            
            <!-- Flecha/triángulo amarillo -->
            <path class="forma-flotante-3" d="M220 480 L280 520 L240 600 Z" fill="#eab308" opacity="0.2"/>
            
            <!-- Forma azul irregular -->
            <path class="forma-flotante-4" d="M160 700 Q200 680 240 730 L250 800 Q220 850 170 820 Z" fill="#3b82f6" opacity="0.15"/>
            
            <!-- Círculo pequeño rosa -->
            <circle class="forma-flotante-2" cx="190" cy="550" r="40" fill="#ec4899" opacity="0.18"/>
            
            <!-- Línea decorativa -->
            <path d="M150 100 Q200 150 250 120" stroke="#f97316" stroke-width="4" opacity="0.15" fill="none"/>
            
            <!-- Forma violeta -->
            <ellipse class="forma-flotante-1" cx="210" cy="650" rx="50" ry="70" fill="#8b5cf6" opacity="0.12"/>
        </svg>
    </div>

    <!-- Contenedor principal con z-index -->
    <div class="contenedor-principal">
    <!-- Header Superior con info de contacto y redes sociales -->
    <!-- Header Superior con info de contacto y redes sociales -->
    <div class="bg-blue-900 text-white py-3 text-[10px] uppercase tracking-widest font-semibold">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-2 md:gap-0">
                <!-- Información de Contacto -->
                <div class="flex items-center gap-4 opacity-90">
                    <span>Contacto: +56 58 220 5500</span>
                    <span class="text-blue-400">|</span>
                    <span>reservas@muniarica.cl</span>
                </div>
                
                <!-- Redes Sociales (Texto) -->
                <div class="flex items-center gap-6 opacity-90">
                    <a href="https://www.facebook.com/MunicipalidaddeArica" target="_blank" class="hover:text-blue-300 transition-colors">Facebook</a>
                    <a href="https://www.instagram.com/muniarica" target="_blank" class="hover:text-blue-300 transition-colors">Instagram</a>
                    <a href="https://www.youtube.com/channel/UCcVtpRl__F8KinypQGhO7VA/feed" target="_blank" class="hover:text-blue-300 transition-colors">Youtube</a>
                    <a href="https://open.spotify.com/user/muniarica" target="_blank" class="hover:text-blue-300 transition-colors">Spotify</a>
                    <a href="https://www.tiktok.com/@muniarica" target="_blank" class="hover:text-blue-300 transition-colors">Tiktok</a>
                    <a href="https://www.flickr.com/photos/muniarica" target="_blank" class="hover:text-blue-300 transition-colors">Flickr</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Principal -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo y Título -->
                <div class="flex items-center space-x-2 xl:space-x-4">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 xl:space-x-3">
                        <!-- Logo de la Municipalidad -->
                        <img src="{{ asset('images/logos/logo-header.png') }}" 
                             alt="Municipalidad de Arica" 
                             class="h-12 xl:h-16 w-auto"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <!-- Fallback: Icono SVG si no hay logo -->
                        <div class="w-12 h-12 xl:w-16 xl:h-16 bg-primary rounded-full items-center justify-center hidden">
                            <svg class="w-8 h-8 xl:w-10 xl:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-lg xl:text-xl font-bold text-primary">Sistema de Reservas</h1>
                            <p class="text-xs xl:text-sm text-gray-600 hidden lg:block">Recintos Deportivos Arica</p>
                        </div>
                    </a>
                </div>
                
                <!-- Navegación Desktop -->
                <!-- Navegación Desktop -->
                <nav class="hidden xl:flex items-center space-x-8">
                    <a href="{{ route('home') }}" 
                       class="nav-link text-sm font-semibold tracking-wide uppercase py-5 border-b-2 transition-colors {{ request()->routeIs('home') ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-200' }}">
                        Inicio
                    </a>

                    <a href="{{ route('reglamentos') }}" 
                       class="nav-link text-sm font-semibold tracking-wide uppercase py-5 border-b-2 transition-colors {{ request()->routeIs('reglamentos') ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-200' }}">
                        Reglamentos
                    </a>
                    
                    <a href="{{ route('cancelacion.formulario') }}" 
                       class="nav-link text-sm font-semibold tracking-wide uppercase py-5 border-b-2 transition-colors {{ request()->routeIs('cancelacion.formulario') ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-200' }}">
                        Cancelar
                    </a>
                    
                    <a href="{{ route('segunda-etapa') }}" 
                       class="nav-link text-sm font-semibold tracking-wide uppercase py-5 border-b-2 transition-colors {{ request()->routeIs('segunda-etapa') ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-orange-600 hover:border-orange-200' }}">
                        Segunda Etapa
                    </a>
                    
                    
                    @auth
                        <!-- Menú Administrativo -->
                        <div class="h-6 w-px bg-gray-200 mx-2"></div>
                        
                        <a href="{{ route('admin.dashboard') }}" 
                           class="nav-link text-sm font-semibold tracking-wide uppercase py-5 border-b-2 transition-colors {{ request()->routeIs('admin.dashboard') ? 'border-secondary text-secondary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-200' }}">
                            Panel Admin
                        </a>
                        @if(auth()->user()->role !== 'encargado_recinto')
                            <a href="{{ route('admin.recintos.index') }}" 
                            class="nav-link text-sm font-semibold tracking-wide uppercase py-5 border-b-2 transition-colors {{ request()->routeIs('admin.recintos.*') ? 'border-secondary text-secondary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-200' }}">
                                Recintos
                            </a>
                            <a href="{{ route('admin.eventos.index') }}" 
                            class="nav-link text-sm font-semibold tracking-wide uppercase py-5 border-b-2 transition-colors {{ request()->routeIs('admin.eventos.*') ? 'border-secondary text-secondary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-200' }}">
                                Eventos
                            </a>
                        @endif
                        
                        <form method="POST" action="{{ route('logout') }}" class="inline ml-4">
                            @csrf
                            <button type="submit" 
                                    class="px-4 py-2 border border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 transition-colors uppercase tracking-wide">
                                Salir
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" 
                           class="ml-4 px-6 py-2 bg-primary text-white text-sm font-semibold hover:bg-blue-800 transition-colors uppercase tracking-wide shadow-sm hover:shadow">
                            Admin
                        </a>
                    @endauth
                </nav>

                <!-- Botón Menú Móvil -->
                <button id="mobile-menu-button" class="xl:hidden text-primary focus:outline-none" aria-label="Abrir menú">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Menú Móvil -->
            <nav id="mobile-menu" class="hidden xl:hidden border-t border-gray-100 bg-white shadow-lg">
                <div class="container mx-auto px-4 py-4 max-h-[70vh] overflow-y-auto">
                    <div class="space-y-1">
                        <a href="{{ route('home') }}" 
                           class="block px-4 py-3 rounded-lg text-sm font-bold uppercase tracking-widest transition-all {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-900 border-l-4 border-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            Inicio
                        </a>

                        <a href="{{ route('reglamentos') }}" 
                           class="block px-4 py-3 rounded-lg text-sm font-bold uppercase tracking-widest transition-all {{ request()->routeIs('reglamentos') ? 'bg-blue-50 text-blue-900 border-l-4 border-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            Reglamentos
                        </a>
                        
                        <a href="{{ route('cancelacion.formulario') }}" 
                           class="block px-4 py-3 rounded-lg text-sm font-bold uppercase tracking-widest transition-all {{ request()->routeIs('cancelacion.formulario') ? 'bg-blue-50 text-blue-900 border-l-4 border-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            Cancelar
                        </a>
                        
                        <a href="{{ route('segunda-etapa') }}" 
                           class="block px-4 py-3 rounded-lg text-sm font-bold uppercase tracking-widest transition-all {{ request()->routeIs('segunda-etapa') ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            Segunda Etapa
                        </a>
                        
                        @auth
                            <div class="border-t border-gray-100 my-4 pt-4">
                                <p class="px-4 mb-2 text-xs font-black text-gray-400 uppercase tracking-widest">Administración</p>
                                
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="block px-4 py-3 rounded-lg text-sm font-bold uppercase tracking-widest transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-900 border-l-4 border-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    Dashboard
                                </a>
                                
                                @if(auth()->user()->role !== 'encargado_recinto')
                                    <a href="{{ route('admin.recintos.index') }}" 
                                       class="block px-4 py-3 rounded-lg text-sm font-bold uppercase tracking-widest transition-all {{ request()->routeIs('admin.recintos.*') ? 'bg-blue-50 text-blue-900 border-l-4 border-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                        Recintos
                                    </a>
                                    <a href="{{ route('admin.eventos.index') }}" 
                                       class="block px-4 py-3 rounded-lg text-sm font-bold uppercase tracking-widest transition-all {{ request()->routeIs('admin.eventos.*') ? 'bg-blue-50 text-blue-900 border-l-4 border-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                        Eventos
                                    </a>
                                    <a href="{{ route('admin.estadisticas.index') }}" 
                                       class="block px-4 py-3 rounded-lg text-sm font-bold uppercase tracking-widest transition-all {{ request()->routeIs('admin.estadisticas.*') ? 'bg-blue-50 text-blue-900 border-l-4 border-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                        Estadísticas
                                    </a>
                                @endif
                                
                                <form method="POST" action="{{ route('logout') }}" class="mt-4 px-2">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full text-center px-4 py-3 rounded-lg bg-red-50 text-red-600 border border-red-100 font-bold uppercase tracking-widest hover:bg-red-100 transition-colors shadow-sm">
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="border-t border-gray-100 my-4 pt-4 px-2">
                                <a href="{{ route('login') }}" 
                                   class="block w-full text-center px-4 py-3 rounded-lg bg-blue-900 text-white font-bold uppercase tracking-widest hover:bg-blue-800 transition-colors shadow-md">
                                    Acceso Admin
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Alertas -->
    <div class="container mx-auto px-4 mt-4">
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-r-lg shadow-sm mb-4 flex items-center">
                <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-r-lg shadow-sm mb-4 flex items-center">
                <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-r-lg shadow-sm mb-4">
                <div class="flex items-start">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-semibold mb-2">Se encontraron los siguientes errores:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Contenido Principal -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer - Estilo Parque Centenario -->
    <!-- Footer - Nuevo Diseño Premium -->
    <footer class="bg-[#050b1a] text-white pt-12 pb-8">
        <div class="container mx-auto px-6 lg:px-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Columna 1: Branding e Información (4 columnas) -->
                <div class="lg:col-span-4 flex flex-col justify-between h-full">
                    <div>
                        <!-- Logo / Título -->
                        <div class="mb-6">
                            <img src="{{ asset('images/logos/logo-footer.png') }}" 
                                 alt="Municipalidad de Arica" 
                                 class="h-16 w-auto brightness-0 invert opacity-90"
                                 onerror="this.style.display='none'">
                        </div>

                        <!-- Dirección -->
                        <div class="border-t border-gray-800 pt-6 pb-6">
                            <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3">Dirección</h4>
                            <p class="text-sm font-medium text-gray-300 uppercase tracking-wider leading-relaxed">
                                Av. Comandante San Martín 450, Arica
                            </p>
                        </div>

                        <!-- Contacto -->
                        <div class="pb-6">
                            <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3">Contacto</h4>
                            <div class="flex flex-col space-y-1">
                                <a href="mailto:deportes@muniarica.cl" class="text-sm font-medium text-gray-300 uppercase tracking-wider hover:text-white transition-colors">
                                    deportes@muniarica.cl
                                </a>
                                <span class="text-sm font-medium text-gray-300 uppercase tracking-wider">
                                    +56 58 220 5500
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Eslogan -->
                    <div class="border-t border-gray-800 pt-8 mt-4">
                        <p class="text-xs text-gray-500 uppercase tracking-widest leading-relaxed max-w-sm">
                            Impulsando la excelencia atlética y el bienestar comunitario en el corazón del norte de Chile.
                        </p>
                    </div>
                </div>

                <!-- Columna 2: Imagen Vertical (2 columnas) -->
                <div class="lg:col-span-2 hidden lg:block">
                    <div class="h-full w-full pl-4 border-l border-gray-800/50 flex items-center justify-center">
                        <div class="relative w-full h-[400px] overflow-hidden">
                            <img src="{{ asset('images/footer-estadio.jpg') }}" 
                                 alt="Estadio Arica" 
                                 class="w-full h-full object-cover"
                                 onerror="this.src='https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=500&h=1000&fit=crop'">
                            <!-- Decorative vertical lines -->
                            <div class="absolute top-0 bottom-0 left-[-10px] w-[1px] bg-gray-700 h-full"></div>
                        </div>
                    </div>
                </div>

                <!-- Columna 3: Navegación (3 columnas) -->
                <div class="lg:col-span-3 pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <!-- Explorar -->
                        <div>
                            <h4 class="text-[11px] font-bold text-white uppercase tracking-[0.2em] mb-8">Explorar</h4>
                            <ul class="space-y-6">
                                <li>
                                    <a href="{{ route('home') }}" class="text-[11px] text-gray-400 hover:text-white uppercase tracking-widest transition-colors">
                                        Instalaciones
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('segunda-etapa') }}" class="text-[11px] text-gray-400 hover:text-white uppercase tracking-widest transition-colors">
                                        Segunda Etapa
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('home') }}#eventos" class="text-[11px] text-gray-400 hover:text-white uppercase tracking-widest transition-colors">
                                        Eventos
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('reglamentos') }}" class="text-[11px] text-gray-400 hover:text-white uppercase tracking-widest transition-colors">
                                        Normativas
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Institución -->
                        <div>
                            <h4 class="text-[11px] font-bold text-white uppercase tracking-[0.2em] mb-8">Institución</h4>
                            <ul class="space-y-6">
                                <li>
                                    <a href="https://transparencia.municipalidaddearica.cl/" target="_blank" class="text-[11px] text-gray-400 hover:text-white uppercase tracking-widest transition-colors">
                                        Transparencia
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.muniarica.cl" target="_blank" class="text-[11px] text-gray-400 hover:text-white uppercase tracking-widest transition-colors">
                                        Municipalidad
                                    </a>
                                </li>
                                <li>
                                    <a href="https://transparencia.municipalidaddearica.cl/page.php?p=380" target="_blank" class="text-[11px] text-gray-400 hover:text-white uppercase tracking-widest transition-colors">
                                        Concejo
                                    </a>
                                </li>
                                @guest
                                <li>
                                    <a href="{{ route('login') }}" class="text-[11px] text-gray-400 hover:text-white uppercase tracking-widest transition-colors">
                                        Acceso Admin
                                    </a>
                                </li>
                                @endguest
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Columna 4: Social (3 columnas) -->
                <div class="lg:col-span-3 pt-4 lg:pl-10">
                    <h4 class="text-[11px] font-bold text-white uppercase tracking-[0.2em] mb-8">Social</h4>
                    <ul class="space-y-6">
                        <li>
                            <a href="https://www.instagram.com/muniarica" target="_blank" class="text-[11px] text-gray-400 hover:text-white uppercase tracking-widest transition-colors block">
                                INSTAGRAM
                            </a>
                        </li>
                        <li>
                            <a href="https://www.facebook.com/MunicipalidaddeArica" target="_blank" class="text-[11px] text-gray-400 hover:text-white uppercase tracking-widest transition-colors block">
                                FACEBOOK
                            </a>
                        </li>
                        <li>
                            <a href="https://www.youtube.com/channel/UCcVtpRl__F8KinypQGhO7VA/feed" target="_blank" class="text-[11px] text-gray-400 hover:text-white uppercase tracking-widest transition-colors block">
                                YOUTUBE
                            </a>
                        </li>
                        <li>
                            <a href="https://www.tiktok.com/@muniarica" target="_blank" class="text-[11px] text-gray-400 hover:text-white uppercase tracking-widest transition-colors block">
                                TIKTOK
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-800 mt-10 pt-6 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-[10px] text-gray-500 uppercase tracking-widest text-center md:text-left">
                    © 2026 Ilustre Municipalidad de Arica — Pulsando Deportes
                </p>
                <div class="flex gap-8">
                    <a href="#" class="text-[10px] text-gray-500 hover:text-white uppercase tracking-widest transition-colors">Privacidad</a>
                    <a href="#" class="text-[10px] text-gray-500 hover:text-white uppercase tracking-widest transition-colors">Términos</a>
                    <a href="#" class="text-[10px] text-gray-500 hover:text-white uppercase tracking-widest transition-colors">Accesibilidad</a>
                </div>
            </div>
        </div>
    </footer>
    </div>
    <!-- Fin del contenedor principal -->

    <!-- Modal de Confirmación Personalizado -->
    <div id="customConfirmModal" class="fixed inset-0 z-50 hidden">
        <!-- Overlay oscuro -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity" id="confirmModalOverlay"></div>
        
        <!-- Contenedor del modal -->
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all w-full max-w-md" id="confirmModalContent">
                    <!-- Icono y Header -->
                    <div class="p-6 pb-4">
                        <div class="flex items-center justify-center mb-4">
                            <div id="confirmModalIcon" class="w-16 h-16 rounded-full flex items-center justify-center bg-red-100">
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                        
                        <h3 id="confirmModalTitle" class="text-xl font-bold text-gray-900 text-center mb-2">
                            ¿Estás seguro?
                        </h3>
                        
                        <p id="confirmModalMessage" class="text-gray-600 text-center">
                            Esta acción no se puede deshacer.
                        </p>
                    </div>
                    
                    <!-- Botones -->
                    <div class="flex gap-3 p-4 pt-2 bg-gray-50">
                        <button type="button" id="confirmModalCancel" 
                                class="flex-1 px-4 py-3 text-gray-700 font-semibold bg-gray-200 hover:bg-gray-300 rounded-xl transition-colors">
                            Cancelar
                        </button>
                        <button type="button" id="confirmModalAccept" 
                                class="flex-1 px-4 py-3 text-white font-semibold bg-red-600 hover:bg-red-700 rounded-xl transition-colors">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sistema de Modal de Confirmación Personalizado
        window.customConfirm = function(options) {
            return new Promise((resolve) => {
                const modal = document.getElementById('customConfirmModal');
                const title = document.getElementById('confirmModalTitle');
                const message = document.getElementById('confirmModalMessage');
                const acceptBtn = document.getElementById('confirmModalAccept');
                const cancelBtn = document.getElementById('confirmModalCancel');
                const icon = document.getElementById('confirmModalIcon');
                
                // Configurar textos
                title.textContent = options.title || '¿Estás seguro?';
                message.textContent = options.message || 'Esta acción no se puede deshacer.';
                acceptBtn.textContent = options.confirmText || 'Confirmar';
                cancelBtn.textContent = options.cancelText || 'Cancelar';
                
                // Configurar colores según el tipo
                const type = options.type || 'danger';
                acceptBtn.className = 'flex-1 px-4 py-3 text-white font-semibold rounded-xl transition-colors ';
                
                if (type === 'danger') {
                    icon.className = 'w-16 h-16 rounded-full flex items-center justify-center bg-red-100';
                    icon.innerHTML = '<svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
                    acceptBtn.className += 'bg-red-600 hover:bg-red-700';
                } else if (type === 'success') {
                    icon.className = 'w-16 h-16 rounded-full flex items-center justify-center bg-green-100';
                    icon.innerHTML = '<svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                    acceptBtn.className += 'bg-green-600 hover:bg-green-700';
                } else if (type === 'warning') {
                    icon.className = 'w-16 h-16 rounded-full flex items-center justify-center bg-yellow-100';
                    icon.innerHTML = '<svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
                    acceptBtn.className += 'bg-yellow-600 hover:bg-yellow-700';
                } else if (type === 'info') {
                    icon.className = 'w-16 h-16 rounded-full flex items-center justify-center bg-blue-100';
                    icon.innerHTML = '<svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                    acceptBtn.className += 'bg-blue-600 hover:bg-blue-700';
                }
                
                // Mostrar modal
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                // Limpiar eventos anteriores
                const newAcceptBtn = acceptBtn.cloneNode(true);
                const newCancelBtn = cancelBtn.cloneNode(true);
                acceptBtn.parentNode.replaceChild(newAcceptBtn, acceptBtn);
                cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
                
                // Eventos de cierre
                function closeModal(result) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                    resolve(result);
                }
                
                newAcceptBtn.addEventListener('click', () => closeModal(true));
                newCancelBtn.addEventListener('click', () => closeModal(false));
                document.getElementById('confirmModalOverlay').addEventListener('click', () => closeModal(false));
                
                // Cerrar con Escape
                document.addEventListener('keydown', function escHandler(e) {
                    if (e.key === 'Escape') {
                        closeModal(false);
                        document.removeEventListener('keydown', escHandler);
                    }
                });
            });
        };

        // Toggle menú móvil
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Cerrar menú móvil al hacer clic en un enlace
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobile-menu').classList.add('hidden');
            });
        });
    </script>

    <!-- n8n Chat Widget - Asistente Virtual Municipal -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" rel="stylesheet" />
    <script type="module">
        import { createChat } from 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/chat.bundle.es.js';

        createChat({
            webhookUrl: 'http://localhost:5678/webhook/2909c8f6-6893-4bfa-a978-ac78c275d88e/chat',
            mode: 'window',
            showWelcomeScreen: true,
            defaultLanguage: 'es',
            initialMessages: [
                '¡Hola! 👋 Soy el asistente virtual del Sistema de Reservas de Recintos Deportivos de la Municipalidad de Arica.',
                '¿En qué puedo ayudarte hoy? Puedo responder consultas sobre disponibilidad, recintos y el proceso de reserva.'
            ],
            i18n: {
                es: {
                    title: 'Asistente Municipal',
                    subtitle: 'Sistema de Reservas Deportivas',
                    footer: '',
                    getStarted: 'Iniciar Conversación',
                    inputPlaceholder: 'Escribe tu mensaje...',
                    closeButtonTooltip: 'Cerrar chat',
                },
            },
            theme: {
                // Colores institucionales de la municipalidad
                '--chat--color-primary': '#1e3a8a',
                '--chat--color-primary-shade-50': '#3b5998',
                '--chat--color-primary-shade-100': '#1e3a8a',
                '--chat--color-secondary': '#f59e0b',
                '--chat--color-secondary-shade-50': '#fbbf24',
                '--chat--color-white': '#ffffff',
                '--chat--color-light': '#f8fafc',
                '--chat--color-light-shade-50': '#e2e8f0',
                '--chat--color-light-shade-100': '#cbd5e1',
                '--chat--color-medium': '#64748b',
                '--chat--color-dark': '#1e293b',
                '--chat--color-disabled': '#94a3b8',
                '--chat--color-typing': '#64748b',
                '--chat--spacing': '1rem',
                '--chat--border-radius': '0.75rem',
                '--chat--transition-duration': '0.15s',

                '--chat--window--width': '400px',
                '--chat--window--height': '600px',

                '--chat--header-height': 'auto',
                '--chat--header--padding': '1rem',
                '--chat--header--background': 'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%)',
                '--chat--header--color': '#ffffff',

                '--chat--textarea--height': '50px',
                '--chat--message--bot--background': '#f1f5f9',
                '--chat--message--bot--color': '#1e293b',
                '--chat--message--user--background': 'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%)',
                '--chat--message--user--color': '#ffffff',
                '--chat--message--pre--background': '#1e293b',
                
                '--chat--toggle--background': 'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%)',
                '--chat--toggle--hover--background': 'linear-gradient(135deg, #1e40af 0%, #2563eb 100%)',
                '--chat--toggle--active--background': 'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%)',
                '--chat--toggle--color': '#ffffff',
                '--chat--toggle--size': '60px',
            },
        });
    </script>
    <style>
        /* Ajustes adicionales para el chat widget */
        .n8n-chat .chat-toggle {
            box-shadow: 0 4px 20px rgba(30, 58, 138, 0.4);
        }
        .n8n-chat .chat-toggle:hover {
            transform: scale(1.05);
        }
    </style> -->
</body>
</html>