<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Reservas Deportivas')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-800 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold">
                        <a href="{{ route('home') }}">Reservas Deportivas Arica</a>
                    </h1>
                    <p class="text-blue-200 text-sm">Municipalidad de Arica</p>
                </div>
                
                <nav class="hidden md:flex space-x-6">
                    <a href="{{ route('home') }}" class="hover:text-blue-200 transition-colors {{ request()->routeIs('home') ? 'font-bold border-b-2 border-blue-200' : '' }}">
                        Inicio
                    </a>
                    <a href="{{ route('calendario') }}" class="hover:text-blue-200 transition-colors {{ request()->routeIs('calendario') ? 'font-bold border-b-2 border-blue-200' : '' }}">
                        Calendario
                    </a>
                    <a href="{{ route('cancelacion.formulario') }}" class="hover:text-blue-200 transition-colors {{ request()->routeIs('cancelacion.formulario') ? 'font-bold border-b-2 border-blue-200' : '' }}">
                        Cancelar Reserva
                    </a>
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-200 transition-colors {{ request()->routeIs('admin.dashboard') ? 'font-bold border-b-2 border-blue-200' : '' }}">
                            Administración
                        </a>
                        <a href="{{ route('admin.recintos.index') }}" class="hover:text-blue-200 transition-colors {{ request()->routeIs('admin.recintos.*') ? 'font-bold border-b-2 border-blue-200' : '' }}">
                            Recintos
                        </a>
                        <a href="{{ route('admin.eventos.index') }}" class="hover:text-blue-200 transition-colors {{ request()->routeIs('admin.eventos.*') ? 'font-bold border-b-2 border-blue-200' : '' }}">
                            Eventos
                        </a>
                        <a href="{{ route('admin.reservas.index') }}" class="hover:text-blue-200 transition-colors {{ request()->routeIs('admin.reservas.*') ? 'font-bold border-b-2 border-blue-200' : '' }}">
                            Reservas
                        </a>
                        <a href="{{ route('admin.estadisticas.index') }}" class="hover:text-blue-200 transition-colors {{ request()->routeIs('admin.estadisticas.index') ? 'font-bold border-b-2 border-blue-200' : '' }}">
                            Estadísticas
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-blue-200 transition-colors">
                                Cerrar Sesión
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-blue-200 transition-colors">
                            Iniciar Sesión
                        </a>
                    @endauth
                </nav>

                <button id="mobile-menu-button" class="md:hidden text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <nav id="mobile-menu" class="hidden md:hidden mt-4 space-y-2">
                <a href="{{ route('home') }}" class="block py-2 hover:text-blue-200 transition-colors {{ request()->routeIs('home') ? 'font-bold' : '' }}">
                    Inicio
                </a>
                <a href="{{ route('calendario') }}" class="block py-2 hover:text-blue-200 transition-colors {{ request()->routeIs('calendario') ? 'font-bold' : '' }}">
                    Calendario
                </a>
                <a href="{{ route('cancelacion.formulario') }}" class="block py-2 hover:text-blue-200 transition-colors {{ request()->routeIs('cancelacion.formulario') ? 'font-bold' : '' }}">
                    Cancelar Reserva
                </a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="block py-2 hover:text-blue-200 transition-colors {{ request()->routeIs('admin.dashboard') ? 'font-bold' : '' }}">
                        Administración
                    </a>
                    <a href="{{ route('admin.recintos.index') }}" class="block py-2 hover:text-blue-200 transition-colors {{ request()->routeIs('admin.recintos.*') ? 'font-bold' : '' }}">
                        Recintos
                    </a>
                    <a href="{{ route('admin.eventos.index') }}" class="block py-2 hover:text-blue-200 transition-colors {{ request()->routeIs('admin.eventos.*') ? 'font-bold' : '' }}">
                        Eventos
                    </a>
                    <a href="{{ route('admin.reservas.index') }}" class="block py-2 hover:text-blue-200 transition-colors {{ request()->routeIs('admin.reservas.*') ? 'font-bold' : '' }}">
                        Reservas
                    </a>
                    <a href="{{ route('admin.estadisticas.index') }}" class="block py-2 hover:text-blue-200 transition-colors {{ request()->routeIs('admin.estadisticas.index') ? 'font-bold' : '' }}">
                        Estadísticas
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left py-2 hover:text-blue-200 transition-colors">
                            Cerrar Sesión
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block py-2 hover:text-blue-200 transition-colors">
                        Iniciar Sesión
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mx-4 mt-4 rounded relative">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mx-4 mt-4 rounded relative">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mx-4 mt-4 rounded relative">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} Municipalidad de Arica - Sistema de Reservas Deportivas</p>
            <p class="text-gray-400 text-sm mt-2">
                Epicentro 1 • Epicentro 2 • Fortín Sotomayor • Piscina Olímpica
            </p>
        </div>
    </footer>

    <script>
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>