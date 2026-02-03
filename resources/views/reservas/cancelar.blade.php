@extends('layouts.app')

@section('title', 'Cancelar Reserva')

@section('content')
<div class="max-w-3xl mx-auto px-6 lg:px-8 py-20">
    
    <!-- Header -->
    <div class="text-center mb-16">
        <h1 class="text-5xl md:text-6xl font-serif font-bold text-gray-900 mb-4 tracking-tight">Cancelar Reserva</h1>
        <p class="text-gray-500 font-light text-lg">Ingresa el código de cancelación que recibiste por correo electrónico</p>
    </div>

    <!-- Info Box -->
    <div class="border border-blue-100 bg-blue-50/30 rounded-xl p-8 text-center mb-12">
        <h3 class="text-xs font-bold text-blue-900 uppercase tracking-[0.2em] mb-4">¿Dónde encuentro mi código?</h3>
        <p class="text-gray-600 leading-relaxed text-sm">
            El código de cancelación fue enviado en el correo de aprobación de tu reserva.<br>
            Tiene el formato: <span class="font-mono font-medium text-blue-600">XXXXXXXX-XXXXXXXX</span>
        </p>
    </div>

    <!-- Formulario -->
    <div class="bg-white border border-gray-100 rounded-2xl p-10 shadow-sm mb-16">
        <form method="POST" action="{{ route('cancelacion.buscar') }}">
            @csrf
            
            <div class="mb-8">
                <label for="codigo" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">
                    <span class="text-red-500 mr-1">*</span> Código de Cancelación
                </label>
                <input type="text" 
                       id="codigo" 
                       name="codigo" 
                       value="{{ old('codigo') }}"
                       placeholder="EJ: ABC12DEF-GH34IJ56"
                       required
                       maxlength="17"
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg px-6 py-4 text-center text-xl font-mono text-gray-800 placeholder-gray-300 uppercase focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all @error('codigo') border-red-500 bg-red-50 @enderror">
                
                @error('codigo')
                    <p class="mt-2 text-red-500 text-sm flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
                
                <p class="text-center text-xs text-gray-400 mt-3 italic">El código no distingue entre mayúsculas y minúsculas</p>
            </div>

            <button type="submit" 
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-6 rounded-lg transition-all duration-300 uppercase tracking-widest text-sm shadow-md hover:shadow-xl hover:-translate-y-0.5">
                Buscar Mi Reserva
            </button>
        </form>
    </div>

    <!-- Ayuda -->
    <div class="max-w-xl mx-auto">
        <h3 class="text-xs font-bold text-gray-900 uppercase tracking-widest mb-6">¿Necesitas ayuda?</h3>
        <ul class="space-y-4 text-sm text-gray-500">
            <li class="flex items-start">
                <span class="w-2 h-2 mt-1.5 bg-gray-300 rounded-full mr-4 flex-shrink-0"></span>
                <span>Si no encuentras el correo, revisa tu carpeta de spam, promociones o correo no deseado.</span>
            </li>
            <li class="flex items-start">
                <span class="w-2 h-2 mt-1.5 bg-gray-300 rounded-full mr-4 flex-shrink-0"></span>
                <span>Solo se pueden cancelar reservas que aún no hayan ocurrido (fechas futuras).</span>
            </li>
            <li class="flex items-start">
                <span class="w-2 h-2 mt-1.5 bg-gray-300 rounded-full mr-4 flex-shrink-0"></span>
                <span>Si tienes problemas técnicos persistentes, contacta directamente con el Departamento de Deportes.</span>
            </li>
        </ul>
    </div>
</div>
@endsection