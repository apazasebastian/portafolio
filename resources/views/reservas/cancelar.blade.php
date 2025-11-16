@extends('layouts.app')

@section('title', 'Cancelar Reserva')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mb-4">
                <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Cancelar Reserva</h1>
            <p class="text-gray-600">Ingresa el código de cancelación que recibiste por correo electrónico</p>
        </div>

        <!-- Información -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
            <div class="flex">
                <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div class="text-sm text-blue-700">
                    <p class="font-medium mb-1">¿Dónde encuentro mi código?</p>
                    <p>El código de cancelación fue enviado en el correo de aprobación de tu reserva. 
                    Tiene el formato: <strong>XXXXXXXX-XXXXXXXX</strong></p>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form method="POST" action="{{ route('cancelacion.buscar') }}">
                @csrf
                
                <div class="mb-6">
                    <label for="codigo" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Código de Cancelación
                    </label>
                    <input type="text" 
                           id="codigo" 
                           name="codigo" 
                           value="{{ old('codigo') }}"
                           placeholder="Ej: ABC12DEF-GH34IJ56"
                           required
                           maxlength="17"
                           class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 text-center text-lg font-mono uppercase focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('codigo') border-red-500 @enderror">
                    
                    @error('codigo')
                        <div class="mt-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-start">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm">{{ $message }}</span>
                        </div>
                    @enderror
                    
                    <p class="text-xs text-gray-500 mt-2">El código no distingue entre mayúsculas y minúsculas</p>
                </div>

                <button type="submit" 
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-lg hover:shadow-xl">
                    Buscar Mi Reserva
                </button>
            </form>
        </div>

        <!-- Ayuda adicional -->
        <div class="mt-8 bg-gray-50 rounded-lg p-6">
            <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
                ¿Necesitas ayuda?
            </h3>
            <ul class="text-sm text-gray-600 space-y-2">
                <li class="flex items-start">
                    <span class="text-blue-600 mr-2">•</span>
                    <span>Si no encuentras el correo, revisa tu carpeta de spam</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 mr-2">•</span>
                    <span>Solo se pueden cancelar reservas que aún no hayan ocurrido</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 mr-2">•</span>
                    <span>Si tienes problemas, contacta con el Departamento de Deportes</span>
                </li>
            </ul>
        </div>

        <!-- Volver al inicio -->
        <div class="text-center mt-8">
            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                ← Volver al inicio
            </a>
        </div>

    </div>
</div>
@endsection