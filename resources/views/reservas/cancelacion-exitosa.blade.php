@extends('layouts.app')

@section('title', 'Cancelación Exitosa')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        
        <!-- Mensaje de Éxito -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-6 animate-pulse">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">¡Cancelación Exitosa!</h1>
            <p class="text-lg text-gray-600">Tu reserva ha sido cancelada correctamente</p>
        </div>

        <!-- Detalles de la Cancelación -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                Reserva Cancelada
            </h2>
            
            <div class="space-y-3">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Recinto:</span>
                    <span class="font-semibold text-gray-800">{{ $reserva->recinto->nombre }}</span>
                </div>
                
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Organización:</span>
                    <span class="font-semibold text-gray-800">{{ $reserva->nombre_organizacion }}</span>
                </div>
                
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Fecha:</span>
                    <span class="font-semibold text-gray-800">{{ $reserva->fecha_reserva->format('d/m/Y') }}</span>
                </div>
                
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Horario:</span>
                    <span class="font-semibold text-gray-800">{{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Cancelada el:</span>
                    <span class="font-semibold text-gray-800">{{ $reserva->fecha_cancelacion->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Confirmación de Correo -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
            <div class="flex">
                <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
                <div class="text-sm text-blue-700">
                    <p class="font-medium mb-1">📧 Correo de Confirmación Enviado</p>
                    <p>Hemos enviado un correo de confirmación a: <strong>{{ $reserva->email }}</strong></p>
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                ¿Qué sigue ahora?
            </h3>
            <ul class="text-sm text-gray-600 space-y-2">
                <li class="flex items-start">
                    <span class="text-green-600 mr-2 mt-1">✓</span>
                    <span>El horario quedará disponible inmediatamente para otras organizaciones</span>
                </li>
                <li class="flex items-start">
                    <span class="text-green-600 mr-2 mt-1">✓</span>
                    <span>Puedes realizar una nueva reserva en cualquier momento ingresando al sistema</span>
                </li>
                <li class="flex items-start">
                    <span class="text-green-600 mr-2 mt-1">✓</span>
                    <span>Si tienes dudas, puedes contactar al Departamento de Deportes</span>
                </li>
            </ul>
        </div>

        <!-- Botones de Acción -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('home') }}" 
               class="text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors shadow-md">
                Volver al Inicio
            </a>
            
            <a href="{{ route('calendario') }}" 
               class="text-center bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-colors shadow-md">
                Hacer Nueva Reserva
            </a>
        </div>

    </div>
</div>
@endsection