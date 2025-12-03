@extends('layouts.app')

@section('title', 'Detalle de Incidencia')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        
        <!-- Breadcrumb -->
        <div class="mb-6">
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Dashboard
            </a>
        </div>

        <!-- Mensajes de Éxito -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 rounded-full mr-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Incidencia #{{ $incidencia->id }}</h1>
                        <p class="text-gray-600">Reportada el {{ $incidencia->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <!-- Estado Actual -->
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold border-2
                    {{ $incidencia->estado === 'reportada' ? 'bg-yellow-100 text-yellow-800 border-yellow-400' : '' }}
                    {{ $incidencia->estado === 'en_revision' ? 'bg-blue-100 text-blue-800 border-blue-400' : '' }}
                    {{ $incidencia->estado === 'resuelta' ? 'bg-green-100 text-green-800 border-green-400' : '' }}">
                    {{ ucfirst(str_replace('_', ' ', $incidencia->estado)) }}
                </span>
            </div>
        </div>

        <!-- Información de la Incidencia -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Detalles de la Incidencia</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Tipo</label>
                    <p class="text-gray-900 font-semibold">
                        @if($incidencia->tipo === 'problema_posuso')
                            Problema Post-Uso
                        @elseif($incidencia->tipo === 'dano')
                            Daño en Instalaciones
                        @else
                            Otro
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Fecha de Reporte</label>
                    <p class="text-gray-900 font-semibold">
                        {{ $incidencia->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">Descripción</label>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <p class="text-gray-900 whitespace-pre-line">{{ $incidencia->descripcion }}</p>
                </div>
            </div>
        </div>

        <!-- Información de la Reserva Relacionada -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Reserva Relacionada</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div>
                    <label class="block text-sm text-gray-600">ID de Reserva</label>
                    <p class="font-semibold text-gray-900">#{{ $incidencia->reserva->id }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Recinto</label>
                    <p class="font-semibold text-gray-900">{{ $incidencia->reserva->recinto->nombre }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Organización</label>
                    <p class="font-semibold text-gray-900">{{ $incidencia->reserva->nombre_organizacion }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Representante</label>
                    <p class="font-semibold text-gray-900">{{ $incidencia->reserva->representante_nombre }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Fecha de Uso</label>
                    <p class="font-semibold text-gray-900">{{ $incidencia->reserva->fecha_reserva->format('d/m/Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Horario</label>
                    <p class="font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($incidencia->reserva->hora_inicio)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($incidencia->reserva->hora_fin)->format('H:i') }}
                    </p>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.reservas.show', $incidencia->reserva) }}" 
                   class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Ver detalles completos de la reserva
                </a>
            </div>
        </div>

        <!-- Cambiar Estado -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Cambiar Estado</h2>
            
            <form action="{{ route('admin.incidencias.cambiar-estado', $incidencia) }}" method="POST">
                @csrf
                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">
                            Nuevo Estado
                        </label>
                        <select name="estado" id="estado" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="reportada" {{ $incidencia->estado === 'reportada' ? 'selected' : '' }}>
                                Reportada
                            </option>
                            <option value="en_revision" {{ $incidencia->estado === 'en_revision' ? 'selected' : '' }}>
                                En Revisión
                            </option>
                            <option value="resuelta" {{ $incidencia->estado === 'resuelta' ? 'selected' : '' }}>
                                Resuelta
                            </option>
                        </select>
                    </div>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-md transition-colors">
                        Actualizar Estado
                    </button>
                </div>
            </form>
        </div>

        <!-- Acciones -->
        <div class="flex gap-3">
            <a href="{{ route('admin.incidencias.crear', $incidencia->reserva_id) }}" 
               class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-md transition-colors text-center">
                Volver a Incidencias
            </a>
            
            <form action="{{ route('admin.incidencias.destroy', $incidencia) }}" method="POST" class="flex-1"
                  onsubmit="return confirm('¿Está seguro de eliminar esta incidencia? Esta acción no se puede deshacer.')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-md transition-colors">
                    Eliminar Incidencia
                </button>
            </form>
        </div>
    </div>
</div>
@endsection