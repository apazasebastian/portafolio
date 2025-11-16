@extends('layouts.app')

@section('title', 'Confirmar Cancelación')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        
        <!-- Breadcrumb -->
        <div class="mb-6">
            <a href="{{ route('cancelacion.formulario') }}" class="text-blue-600 hover:text-blue-800">
                ← Ingresar otro código
            </a>
        </div>

        <!-- Header -->
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-16 h-16 bg-white bg-opacity-30 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Confirmar Cancelación</h1>
                    <p class="text-yellow-100">Esta acción no se puede deshacer</p>
                </div>
            </div>
        </div>

        <!-- Detalles de la Reserva -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                Detalles de la Reserva
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500 mb-1">Recinto</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $reserva->recinto->nombre }}</p>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500 mb-1">Organización</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $reserva->nombre_organizacion }}</p>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500 mb-1">Fecha de la Reserva</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $reserva->fecha_reserva->format('d/m/Y') }}</p>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500 mb-1">Horario</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }}</p>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500 mb-1">Personas</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $reserva->cantidad_personas }}</p>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500 mb-1">Deporte</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $reserva->deporte }}</p>
                </div>
            </div>
        </div>

        <!-- Formulario de Confirmación -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Motivo de la Cancelación</h3>
            
            <form method="POST" action="{{ route('cancelacion.procesar', $reserva->codigo_cancelacion) }}">
                @csrf
                
                <div class="mb-6">
                    <label for="motivo" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Por favor, indícanos el motivo de la cancelación
                    </label>
                    <textarea id="motivo" 
                              name="motivo" 
                              rows="4" 
                              required
                              maxlength="500"
                              placeholder="Ej: Cambio de fecha del evento, problemas de logística, etc."
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('motivo') border-red-500 @enderror">{{ old('motivo') }}</textarea>
                    
                    @error('motivo')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    
                    <p class="text-xs text-gray-500 mt-1">Máximo 500 caracteres</p>
                </div>

                <!-- Advertencia Final -->
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex">
                        <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-red-700">
                            <p class="font-medium mb-1">⚠️ Advertencia Importante</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Esta acción NO se puede deshacer</li>
                                <li>El horario quedará disponible para otras organizaciones</li>
                                <li>Recibirás un correo de confirmación</li>
                                <li>Deberás realizar una nueva solicitud si deseas reservar nuevamente</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('cancelacion.formulario') }}" 
                       class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-3 px-6 rounded-lg transition-colors">
                        No, Mantener Reserva
                    </a>
                    
                    <button type="submit" 
                            onclick="return confirm('¿Estás completamente seguro de cancelar esta reserva? Esta acción no se puede deshacer.')"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-lg hover:shadow-xl">
                        Sí, Cancelar Reserva
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection