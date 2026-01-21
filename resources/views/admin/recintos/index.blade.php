@extends('layouts.app')

@section('title', 'Gestión de Recintos')

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Gestión de Recintos</h1>
            <p class="text-gray-600">Administra los recintos deportivos disponibles</p>
        </div>
        <a href="{{ route('admin.recintos.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors shadow-md hover:shadow-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Nuevo Recinto
        </a>
    </div>

    <!-- Mensajes de éxito/error -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg mb-6">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg mb-6">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Tabla de Recintos -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recintos as $recinto)
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($recinto->imagen_url)
                                <img src="{{ asset('storage/' . $recinto->imagen_url) }}" 
                                     alt="{{ $recinto->nombre }}" 
                                     class="w-12 h-12 rounded-lg object-cover mr-3">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $recinto->nombre }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($recinto->descripcion, 30) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $recinto->capacidad_maxima }} personas
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        @php
                            $horarios = is_string($recinto->horarios_disponibles) 
                                ? json_decode($recinto->horarios_disponibles, true) 
                                : $recinto->horarios_disponibles;
                        @endphp
                        {{ $horarios['inicio'] ?? '08:00' }} - {{ $horarios['fin'] ?? '23:00' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($recinto->activo)
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">Activo</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 font-semibold">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.recintos.edit', $recinto) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Editar
                            </a>
                            <span class="text-gray-300">|</span>
                            <form method="POST" action="{{ route('admin.recintos.destroy', $recinto) }}" 
                                  class="inline"
                                  onsubmit="return confirm('¿Estás seguro de eliminar este recinto? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <p class="text-lg font-semibold">No hay recintos registrados</p>
                        <p class="text-sm mt-1">Comienza agregando tu primer recinto deportivo</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Paginación -->
        @if($recintos->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $recintos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection