@extends('layouts.app')

@section('title', 'Detalle de Auditoría')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Detalle de Registro de Auditoría</h1>
            <p class="text-gray-600 mt-1">Log #{{ $log->id }}</p>
        </div>
        <a href="{{ route('admin.auditoria.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800">Información del Registro</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Acción -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Acción Realizada</label>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-base font-medium {{ $log->action_color }}">
                                <span class="text-xl mr-2">{{ $log->action_icon }}</span>
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-gray-900">{{ $log->description }}</p>
                        </div>
                    </div>

                    <!-- Fecha y Hora -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
                            <div class="flex items-center text-gray-900">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="font-medium">{{ $log->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hora</label>
                            <div class="flex items-center text-gray-900">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-medium">{{ $log->created_at->format('H:i:s') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Valores Antiguos y Nuevos -->
                    @if($log->old_values || $log->new_values)
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Cambios Realizados</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Valores Antiguos -->
                                @if($log->old_values)
                                    <div>
                                        <label class="block text-sm font-medium text-red-700 mb-2">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Valores Anteriores
                                        </label>
                                        <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                                            @foreach($log->old_values as $key => $value)
                                                <div class="mb-2 last:mb-0">
                                                    <span class="text-xs font-medium text-red-900 uppercase">{{ $key }}:</span>
                                                    <span class="block text-sm text-red-800 font-mono">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Valores Nuevos -->
                                @if($log->new_values)
                                    <div>
                                        <label class="block text-sm font-medium text-green-700 mb-2">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Valores Nuevos
                                        </label>
                                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                            @foreach($log->new_values as $key => $value)
                                                <div class="mb-2 last:mb-0">
                                                    <span class="text-xs font-medium text-green-900 uppercase">{{ $key }}:</span>
                                                    <span class="block text-sm text-green-800 font-mono">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información del Usuario y Sistema -->
        <div class="space-y-6">
            <!-- Usuario -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">Usuario</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nombre</label>
                        <p class="text-gray-900 font-medium">{{ $log->user_name }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                        <p class="text-gray-900">{{ $log->user_email }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Rol</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst(str_replace('_', ' ', $log->user_role)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Información del Sistema -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">Sistema</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Dirección IP</label>
                        <p class="text-gray-900 font-mono text-sm">{{ $log->ip_address }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Navegador</label>
                        <p class="text-gray-700 text-xs break-words">{{ $log->user_agent }}</p>
                    </div>
                </div>
            </div>

            <!-- Información del Modelo Afectado -->
            @if($log->auditable_type && $log->auditable_id)
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-800">Modelo Afectado</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Tipo</label>
                            <p class="text-gray-900 font-mono text-sm">{{ class_basename($log->auditable_type) }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">ID</label>
                            <p class="text-gray-900 font-mono text-sm">#{{ $log->auditable_id }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection