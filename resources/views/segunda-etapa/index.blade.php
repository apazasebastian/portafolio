@extends('layouts.app')

@section('title', 'Segunda Etapa - Sistema de Reservas Deportivas')

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-8">
    <!-- Header de la página -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Segunda Etapa</h1>
        <p class="text-gray-600">Próximas funcionalidades y expansión del sistema</p>
    </div>

    <!-- Banner informativo -->
    <div class="bg-orange-50 border-l-4 border-orange-500 p-4 mb-8 rounded-r">
        <p class="text-orange-800">
            Estamos trabajando para expandir el sistema de reservas con nuevas funcionalidades y más recintos deportivos municipales.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Nuevos Recintos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Nuevos Recintos Deportivos</h2>
            <p class="text-gray-600 mb-4 text-sm">Próximamente estarán disponibles para reserva:</p>
            
            <ul class="space-y-4">
                <li class="border-b border-gray-100 pb-3">
                    <h3 class="font-semibold text-gray-800">Estadio Carlos Dittborn</h3>
                    <p class="text-sm text-gray-600">Estadio principal de la ciudad con capacidad para múltiples disciplinas deportivas.</p>
                </li>
                <li class="border-b border-gray-100 pb-3">
                    <h3 class="font-semibold text-gray-800">Paseo Deportivo Las Machas</h3>
                    <p class="text-sm text-gray-600">Circuito deportivo costero ideal para running, ciclismo y actividades al aire libre.</p>
                </li>
                <li class="border-b border-gray-100 pb-3">
                    <h3 class="font-semibold text-gray-800">Polideportivo Playa Arena Las Machas</h3>
                    <p class="text-sm text-gray-600">Canchas de arena para vóleibol playa, fútbol playa y actividades recreativas.</p>
                </li>
                <li>
                    <h3 class="font-semibold text-gray-800">Estadio Sector Norte (Punta Norte)</h3>
                    <p class="text-sm text-gray-600">Nuevo estadio en el sector norte de Arica con modernas instalaciones.</p>
                </li>
            </ul>
        </div>
        
        <!-- Sistema de Pago -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Sistema de Pago en Línea</h2>
            <p class="text-gray-600 mb-4 text-sm">Implementaremos un sistema de pago integrado:</p>
            
            <ul class="space-y-3">
                <li class="flex items-start">
                    <span class="text-green-500 mr-2">•</span>
                    <div>
                        <span class="font-medium text-gray-800">Pago con Tarjeta</span>
                        <span class="text-gray-600 text-sm"> — Débito y crédito</span>
                    </div>
                </li>
                <li class="flex items-start">
                    <span class="text-green-500 mr-2">•</span>
                    <div>
                        <span class="font-medium text-gray-800">Confirmación Inmediata</span>
                        <span class="text-gray-600 text-sm"> — Reserva al instante</span>
                    </div>
                </li>
                <li class="flex items-start">
                    <span class="text-green-500 mr-2">•</span>
                    <div>
                        <span class="font-medium text-gray-800">Pago Seguro</span>
                        <span class="text-gray-600 text-sm"> — Transacciones protegidas</span>
                    </div>
                </li>
            </ul>
            
            <div class="mt-6 pt-4 border-t border-gray-200">
                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded">
                    En Desarrollo
                </span>
            </div>
        </div>
    </div>
    
    <!-- Más información -->
    <div class="mt-8 bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
        <p class="text-gray-700">
            Para conocer todos los espacios municipales, visita 
            <a href="https://www.muniarica.cl/mejor-ciudad/recintos-municipales" target="_blank" class="text-blue-600 hover:underline">muniarica.cl/recintos-municipales</a>
        </p>
    </div>
</div>
@endsection
