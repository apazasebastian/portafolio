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
            transform: translateY(-2px);
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
    <div class="bg-primary text-white py-2 text-sm">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                        </svg>
                        <span class="hidden sm:inline">Contacto: </span>+56 58 2205500
                    </span>
                    <span class="hidden md:flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        reservas@muniarica.cl
                    </span>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="hidden lg:inline">Municipalidad de Arica</span>
                    <!-- Redes Sociales -->
                    <div class="flex items-center space-x-2">
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/MunicipalidaddeArica" target="_blank" rel="noopener noreferrer" 
                           class="hover:text-blue-300 transition-colors" title="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        
                        <!-- YouTube -->
                        <a href="https://www.youtube.com/channel/UCcVtpRl__F8KinypQGhO7VA/feed" target="_blank" rel="noopener noreferrer" 
                           class="hover:text-red-300 transition-colors" title="YouTube">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>
                        
                        <!-- Instagram -->
                        <a href="https://www.instagram.com/muniarica" target="_blank" rel="noopener noreferrer" 
                           class="hover:text-pink-300 transition-colors" title="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                        
                        <!-- Spotify -->
                        <a href="https://open.spotify.com/user/muniarica" target="_blank" rel="noopener noreferrer" 
                           class="hover:text-green-300 transition-colors" title="Spotify">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                            </svg>
                        </a>
                        
                        <!-- TikTok -->
                        <a href="https://www.tiktok.com/@muniarica" target="_blank" rel="noopener noreferrer" 
                           class="hover:text-pink-300 transition-colors" title="TikTok">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                            </svg>
                        </a>
                        
                        <!-- Flickr -->
                        <a href="https://www.flickr.com/photos/muniarica" target="_blank" rel="noopener noreferrer" 
                           class="hover:text-blue-300 transition-colors" title="Flickr">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M0 12c0 3.074 2.494 5.564 5.565 5.564 3.075 0 5.569-2.49 5.569-5.564S8.641 6.436 5.565 6.436C2.495 6.436 0 8.926 0 12zm12.866 0c0 3.074 2.493 5.564 5.567 5.564C21.496 17.564 24 15.074 24 12s-2.492-5.564-5.564-5.564c-3.075 0-5.57 2.49-5.57 5.564z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Principal -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo y Título -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        <!-- Logo de la Municipalidad -->
                        <img src="{{ asset('images/logos/logo-header.png') }}" 
                             alt="Municipalidad de Arica" 
                             class="h-16 w-auto"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <!-- Fallback: Icono SVG si no hay logo -->
                        <div class="w-16 h-16 bg-primary rounded-full items-center justify-center hidden">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-primary">Sistema de Reservas</h1>
                            <p class="text-sm text-gray-600">Recintos Deportivos Arica</p>
                        </div>
                    </a>
                </div>
                
                <!-- Navegación Desktop -->
                <nav class="hidden lg:flex items-center space-x-1">
                    <a href="{{ route('home') }}" 
                       class="nav-link px-4 py-2 rounded-lg flex items-center {{ request()->routeIs('home') ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                        Inicio
                    </a>
                    <a href="{{ route('calendario') }}" 
                       class="nav-link px-4 py-2 rounded-lg flex items-center {{ request()->routeIs('calendario') ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        Calendario
                    </a>
                    <a href="{{ route('reglamentos') }}" 
                       class="nav-link px-4 py-2 rounded-lg flex items-center {{ request()->routeIs('reglamentos') ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                        Reglamentos
                    </a>
                    <a href="{{ route('cancelacion.formulario') }}" 
                       class="nav-link px-4 py-2 rounded-lg flex items-center {{ request()->routeIs('cancelacion.formulario') ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        Cancelar Reserva
                    </a>
                    <a href="{{ route('segunda-etapa') }}" 
                       class="nav-link px-4 py-2 rounded-lg flex items-center {{ request()->routeIs('segunda-etapa') ? 'bg-orange-500 text-white' : 'bg-gradient-to-r  to-orange text-orange-700 hover:from-orange-100 hover:to-orange-200' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        Segunda Etapa
                    </a>
                    
                    
                    @auth
                        <!-- Menú Administrativo -->
                        <div class="h-8 w-px bg-gray-300 mx-2"></div>
                        
                        <a href="{{ route('admin.dashboard') }}" 
                           class="nav-link px-4 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-secondary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            Panel Admin
                        </a>
                        @if(auth()->user()->role !== 'encargado_recinto')
                            <a href="{{ route('admin.recintos.index') }}" 
                            class="nav-link px-4 py-2 rounded-lg {{ request()->routeIs('admin.recintos.*') ? 'bg-secondary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                                Recintos
                            </a>
                            <a href="{{ route('admin.eventos.index') }}" 
                            class="nav-link px-4 py-2 rounded-lg {{ request()->routeIs('admin.eventos.*') ? 'bg-secondary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                                Eventos
                            </a>
                        @endif
                        
                        <form method="POST" action="{{ route('logout') }}" class="inline ml-2">
                            @csrf
                            <button type="submit" 
                                    class="nav-link px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Salir
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" 
                           class="nav-link px-4 py-2 rounded-lg bg-secondary text-white hover:bg-blue-700 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Acceso Admin
                        </a>
                    @endauth
                </nav>

                <!-- Botón Menú Móvil -->
                <button id="mobile-menu-button" class="lg:hidden text-primary focus:outline-none">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Menú Móvil -->
            <nav id="mobile-menu" class="hidden lg:hidden pb-4 border-t border-gray-200">
                <div class="space-y-1 pt-4">
                    <a href="{{ route('home') }}" 
                       class="block px-4 py-3 rounded-lg flex items-center {{ request()->routeIs('home') ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                        Inicio
                    </a>
                    <a href="{{ route('calendario') }}" 
                       class="block px-4 py-3 rounded-lg flex items-center {{ request()->routeIs('calendario') ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        Calendario
                    </a>
                    <a href="{{ route('reglamentos') }}" 
                       class="block px-4 py-3 rounded-lg flex items-center {{ request()->routeIs('reglamentos') ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                        Reglamentos
                    </a>
                    <a href="{{ route('cancelacion.formulario') }}" 
                       class="block px-4 py-3 rounded-lg flex items-center {{ request()->routeIs('cancelacion.formulario') ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        Cancelar Reserva
                    </a>
                    <a href="{{ route('segunda-etapa') }}" 
                       class="block px-4 py-3 rounded-lg flex items-center {{ request()->routeIs('segunda-etapa') ? 'bg-orange-500 text-white' : 'bg-gradient-to-r from-orange-50 to-orange-100 text-orange-700' }}">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                        </svg>
                        Segunda Etapa
                    </a>
                    
                    @auth
                        <div class="border-t border-gray-200 my-2 pt-2">
                            <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Administración</p>
                        </div>
                        
                        <a href="{{ route('admin.dashboard') }}" 
                           class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-secondary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            Panel Admin
                        </a>
                        @if(auth()->user()->role !== 'encargado_recinto')
                            <a href="{{ route('admin.recintos.index') }}" 
                            class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.recintos.*') ? 'bg-secondary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                                Recintos
                            </a>
                            <a href="{{ route('admin.eventos.index') }}" 
                            class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.eventos.*') ? 'bg-secondary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                                Eventos
                            </a>
                        @endif
                        <a href="{{ route('admin.reservas.index') }}" 
                           class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.reservas.*') ? 'bg-secondary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            Reservas
                        </a>
                        <a href="{{ route('admin.estadisticas.index') }}" 
                           class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.estadisticas.*') ? 'bg-secondary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            Estadísticas
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}" class="mt-2">
                            @csrf
                            <button type="submit" 
                                    class="w-full text-left px-4 py-3 rounded-lg bg-red-600 text-white hover:bg-red-700">
                                Cerrar Sesión
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" 
                           class="block px-4 py-3 rounded-lg bg-secondary text-white hover:bg-blue-700">
                            Acceso Administrativo
                        </a>
                    @endauth
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
    <footer class="bg-gray-900 text-white mt-16">
        <!-- Línea de acento naranja superior -->
        <div class="h-1 bg-gradient-to-r from-orange-500 to-orange-600"></div>
        
        <!-- Header del Footer: Logo + Redes Sociales -->
        <div class="bg-gray-900 border-b border-gray-800">
            <div class="container mx-auto px-4 py-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <!-- Logo y Título -->
                    <div class="flex items-center space-x-4 mb-4 md:mb-0">
                        <img src="{{ asset('images/logos/logo-footer.png') }}" 
                             alt="Municipalidad de Arica" 
                             class="h-16 w-auto brightness-0 invert"
                             onerror="this.style.display='none'">
                        <div>
                            <h3 class="text-2xl font-bold tracking-wide">RECINTOS</h3>
                            <p class="text-gray-400 text-sm uppercase tracking-widest">Deportivos</p>
                        </div>
                    </div>
                    
                    <!-- Redes Sociales -->
                    <div class="flex items-center space-x-4">
                        <a href="https://www.facebook.com/MunicipalidaddeArica" target="_blank" rel="noopener noreferrer" 
                           class="text-gray-400 hover:text-white transition-colors" title="Facebook">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="https://www.youtube.com/channel/UCcVtpRl__F8KinypQGhO7VA/feed" target="_blank" rel="noopener noreferrer" 
                           class="text-gray-400 hover:text-white transition-colors" title="YouTube">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>
                        <a href="https://open.spotify.com/user/muniarica" target="_blank" rel="noopener noreferrer" 
                           class="text-gray-400 hover:text-white transition-colors" title="Spotify">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                            </svg>
                        </a>
                        <a href="https://www.flickr.com/photos/muniarica" target="_blank" rel="noopener noreferrer" 
                           class="text-gray-400 hover:text-white transition-colors" title="Flickr">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M0 12c0 3.074 2.494 5.564 5.565 5.564 3.075 0 5.569-2.49 5.569-5.564S8.641 6.436 5.565 6.436C2.495 6.436 0 8.926 0 12zm12.866 0c0 3.074 2.493 5.564 5.567 5.564C21.496 17.564 24 15.074 24 12s-2.492-5.564-5.564-5.564c-3.075 0-5.57 2.49-5.57 5.564z"/>
                            </svg>
                        </a>
                        <a href="https://www.tiktok.com/@muniarica" target="_blank" rel="noopener noreferrer" 
                           class="text-gray-400 hover:text-white transition-colors" title="TikTok">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                            </svg>
                        </a>
                        <a href="https://www.instagram.com/muniarica" target="_blank" rel="noopener noreferrer" 
                           class="text-gray-400 hover:text-white transition-colors" title="Instagram">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contenido Principal del Footer -->
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                
                <!-- Columna 1: Más Información -->
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-gray-300 mb-4 border-b border-gray-700 pb-2">Más Información</h4>
                    <div class="space-y-3 text-sm">
                        <p class="text-gray-400">
                            <span class="block text-gray-300 font-medium">Dirección:</span>
                            Arica, Región de Arica y Parinacota
                        </p>
                        <p class="text-gray-400">
                            <span class="block text-gray-300 font-medium">E-mail:</span>
                            reservas@muniarica.cl
                        </p>
                        <p class="text-gray-400">
                            <span class="block text-gray-300 font-medium">Teléfonos:</span>
                            +56 58 2205500
                        </p>
                    </div>
                </div>
                
                <!-- Columna 2: Intranet / Enlaces del Sistema -->
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-gray-300 mb-4 border-b border-gray-700 pb-2">Sistema</h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="{{ route('reglamentos') }}" class="text-gray-400 hover:text-white transition-colors">
                                Reglamentos del Recinto
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('calendario') }}" class="text-gray-400 hover:text-white transition-colors">
                                Calendario de Reservas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cancelacion.formulario') }}" class="text-gray-400 hover:text-white transition-colors">
                                Cancelar Reserva
                            </a>
                        </li>
                        @guest
                        <li>
                            <a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition-colors">
                                Acceso Administrativo
                            </a>
                        </li>
                        @endguest
                    </ul>
                </div>
                
                <!-- Columna 3: Ley de Transparencia -->
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-gray-300 mb-4 border-b border-gray-700 pb-2">Ley de Transparencia</h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="https://www.portaltransparencia.cl/PortalPdT/" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                                Portal de Transparencia
                            </a>
                        </li>
                        <li>
                            <a href="https://transparencia.municipalidaddearica.cl/" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                                Transparencia Activa
                            </a>
                        </li>
                        <li>
                            <a href="https://transparencia.municipalidaddearica.cl/page.php?p=380" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                                Actas del Concejo Municipal
                            </a>
                        </li>
                        <li>
                            <a href="https://transparencia.municipalidaddearica.cl/page.php?p=11" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                                Cuenta Pública
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Columna 4: Municipalidad -->
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-gray-300 mb-4 border-b border-gray-700 pb-2">Municipalidad</h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="https://www.muniarica.cl" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                                Sitio Web Municipal
                            </a>
                        </li>
                        @auth
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-white transition-colors">
                                Panel de Administración
                            </a>
                        </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="border-t border-gray-800 py-4">
            <p class="text-center text-sm text-gray-500">
                Ilustre Municipalidad de Arica
            </p>
        </div>
    </footer>
    </div>
    <!-- Fin del contenedor principal -->

    <script>
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
</body>
</html>