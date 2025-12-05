@extends('layouts.app')

@section('title', 'Registro de Auditoría')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Registro de Auditoría</h1>
                <p class="text-gray-600">Historial completo de acciones en el sistema</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-md transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-gray-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gray-100">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $estadisticas['total_logs'] }}</h3>
                    <p class="text-gray-600 text-sm">Total Registros</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $estadisticas['hoy'] }}</h3>
                    <p class="text-gray-600 text-sm">Hoy</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $estadisticas['esta_semana'] }}</h3>
                    <p class="text-gray-600 text-sm">Esta Semana</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $estadisticas['este_mes'] }}</h3>
                    <p class="text-gray-600 text-sm">Este Mes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('admin.auditoria.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            
            <!-- Usuario -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                <select name="user_id" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todos</option>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ request('user_id') == $usuario->id ? 'selected' : '' }}>
                            {{ $usuario->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Acción -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Acción</label>
                <select name="action" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todas</option>
                    @foreach($acciones as $accion)
                        <option value="{{ $accion }}" {{ request('action') == $accion ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $accion)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Fecha Desde -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                       class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Fecha Hasta -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                       class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Botones -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                    Filtrar
                </button>
                <a href="{{ route('admin.auditoria.index') }}" 
                   class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md transition-colors">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Logs -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-semibold text-gray-800">Registros de Auditoría</h2>
        </div>

        <div class="overflow-x-auto">
            @if($logs->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Fecha/Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Acción</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">IP</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase">Detalles</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($logs as $log)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $log->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $log->user_name }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $log->user_role)) }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border
                                    {{ $log->action_color === 'green' ? 'bg-green-100 text-green-800 border-green-400' : '' }}
                                    {{ $log->action_color === 'red' ? 'bg-red-100 text-red-800 border-red-400' : '' }}
                                    {{ $log->action_color === 'yellow' ? 'bg-yellow-100 text-yellow-800 border-yellow-400' : '' }}
                                    {{ $log->action_color === 'orange' ? 'bg-orange-100 text-orange-800 border-orange-400' : '' }}
                                    {{ $log->action_color === 'blue' ? 'bg-blue-100 text-blue-800 border-blue-400' : '' }}
                                    {{ $log->action_color === 'gray' ? 'bg-gray-100 text-gray-800 border-gray-400' : '' }}">
                                    {{ $log->action_icon }} {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700 max-w-md">
                                    {{ $log->description }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs text-gray-500 font-mono">{{ $log->ip_address }}</span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.auditoria.show', $log) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-md transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Paginación -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Mostrando <span class="font-semibold text-gray-900">{{ $logs->firstItem() ?? 0 }}</span> 
                            a <span class="font-semibold text-gray-900">{{ $logs->lastItem() ?? 0 }}</span> 
                            de <span class="font-semibold text-gray-900">{{ $logs->total() }}</span> registros
                        </div>
                        
                        {{ $logs->links() }}
                    </div>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-lg font-medium text-gray-500 mt-4">No hay registros de auditoría</p>
                    <p class="text-sm text-gray-400 mt-1">Los registros aparecerán aquí cuando se realicen acciones en el sistema</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection