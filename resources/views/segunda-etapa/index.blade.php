@extends('layouts.app')

@section('title', 'Segunda Etapa - Sistema de Reservas Deportivas')

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
    <!-- Header de la página -->
    <div class="text-center mb-8">
        <h1 class="text-4xl md:text-5xl font-serif font-bold text-gray-900 mb-3 tracking-tight">Segunda Etapa</h1>
        <p class="text-gray-500 font-light text-lg">Próximas funcionalidades y expansión del sistema de gestión deportiva municipal.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
        <!-- Columna Izquierda: Nuevos Recintos -->
        <div class="bg-white shadow-lg p-8 border border-gray-100 h-full flex flex-col">
            <div>
                <h2 class="text-2xl font-serif font-bold text-gray-900 mb-6">Nuevos Recintos Deportivos</h2>
                <p class="text-gray-500 italic mb-8 border-b border-gray-100 pb-4">Próximamente estarán disponibles para reserva:</p>
                
                <div class="space-y-8">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Estadio Carlos Dittborn</h3>
                        <p class="text-gray-600 leading-relaxed">Estadio principal de la ciudad con capacidad para múltiples disciplinas deportivas.</p>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Paseo Deportivo Las Machas</h3>
                        <p class="text-gray-600 leading-relaxed">Circuito deportivo costero ideal para running, ciclismo y actividades al aire libre.</p>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Polideportivo Playa Arena Las Machas</h3>
                        <p class="text-gray-600 leading-relaxed">Canchas de arena para vóleibol playa, fútbol playa y actividades recreativas.</p>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Estadio Sector Norte (Punta Norte)</h3>
                        <p class="text-gray-600 leading-relaxed">Nuevo estadio en el sector norte de Arica con modernas instalaciones.</p>
                    </div>
                </div>
            </div>

            <!-- Nota de expansión futura (Centrada en el espacio restante) -->
            <div class="flex-1 flex items-center justify-center min-h-[100px] mt-8">
                <p class="text-sm text-gray-400 italic font-medium text-center max-w-xs">
                    La red de recintos municipales continuará expandiéndose en futuras etapas.
                </p>
            </div>
        </div>
        
        <!-- Columna Derecha -->
        <div class="space-y-8">
            <!-- Sistema de Pago en Línea -->
            <div class="bg-white shadow-lg p-8 border border-gray-100">
                <h2 class="text-2xl font-serif font-bold text-gray-900 mb-6">Sistema de Pago en Línea</h2>
                <p class="text-gray-600 mb-6">Implementaremos un sistema de pago integrado para facilitar las reservas.</p>
                
                <ul class="space-y-4 mb-8">
                    <li class="flex items-center">
                        <svg class="h-5 w-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <span class="font-bold text-gray-900">Pago con Tarjeta</span>
                            <span class="text-gray-500 text-sm"> — Débito y crédito</span>
                        </div>
                    </li>
                    <li class="flex items-center">
                        <svg class="h-5 w-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <span class="font-bold text-gray-900">Confirmación Inmediata</span>
                            <span class="text-gray-500 text-sm"> — Reserva al instante</span>
                        </div>
                    </li>
                    <li class="flex items-center">
                        <svg class="h-5 w-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <span class="font-bold text-gray-900">Pago Seguro</span>
                            <span class="text-gray-500 text-sm"> — Transacciones protegidas</span>
                        </div>
                    </li>
                </ul>
                
                <span class="inline-flex items-center px-4 py-2 text-sm font-semibold bg-blue-50 text-blue-700">
                    En Desarrollo
                </span>
            </div>

            <!-- Sección Image de Referencia -->
            <div class="px-2">
                <h3 class="text-xs font-bold text-gray-400 tracking-[0.2em] uppercase mb-4 ml-1">IMAGEN DE REFERENCIA</h3>
                
                <!-- Contenedor de la Imagen / Mockup -->
                <div class="group relative bg-gray-200 overflow-hidden shadow-xl flex items-center justify-center">
                    <!-- Placeholder si la imagen no existe -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 p-6 text-center z-0">
                        <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Imagen de Referencia</span>
                    </div>

                    <!-- Imagen Real (Referenciada) -->
                    <!-- NOTA: El usuario debe colocar la imagen aquí -->
                    <img src="{{ asset('images/referencia-pago.png') }}" 
                         alt="Vista Previa de la Plataforma" 
                         class="relative z-10 w-full h-auto transition-transform duration-700 group-hover:scale-105"
                         onerror="this.style.opacity='0'">
                </div>
                
                <div class="mt-6 bg-white p-6 shadow-lg border border-gray-100 text-center">
                    <h3 class="text-xl font-bold text-blue-900 mb-2">Vista Previa de la Plataforma</h3>
                    <p class="text-gray-600 text-sm">
                        Así se verá el nuevo sistema de reservas y pago integrado una vez completada esta etapa, brindando un acceso rápido y seguro a todos los recintos deportivos municipales.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Link Footer -->
    <div class="mt-16 bg-gray-50 border border-gray-200 p-6 text-center shadow-inner">
        <p class="text-gray-700 font-medium">
            Para conocer todos los espacios municipales actuales, visita 
            <a href="https://www.muniarica.cl/mejor-ciudad/recintos-municipales" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline transition-colors ml-1">
                muniarica.cl/recintos-municipales
            </a>
        </p>
    </div>
</div>
@endsection
