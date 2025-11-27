@extends('layouts.app')

@section('title', 'Administración de Reservas')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                
                <h2 class="text-2xl font-bold mb-6">Listado de Reservas</h2>

                <form method="GET" action="{{ route('admin.reservas.index') }}" class="mb-6 bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        
                        <div>
                            <label for="recinto_id" class="block text-sm font-medium text-gray-700">Recinto</label>
                            <select name="recinto_id" id="recinto_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos los recintos</option>
                                @foreach($recintos as $recinto)
                                    <option value="{{ $recinto->id }}" {{ request('recinto_id') == $recinto->id ? 'selected' : '' }}>
                                        {{ $recinto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                            <select name="estado" id="estado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos los estados</option>
                                <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                                <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                                <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </div>

                        <div>
                            <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha Reserva</label>
                            <input type="date" name="fecha" id="fecha" value="{{ request('fecha') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="flex space-x-2">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition w-full">
                                Filtrar
                            </button>
                            <a href="{{ route('admin.reservas.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition text-center w-full">
                                Limpiar
                            </a>
                        </div>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recinto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha / Hora</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reservas as $reserva)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        #{{ $reserva->id }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $reserva->nombre_organizacion ?? $reserva->representante_nombre }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $reserva->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $reserva->recinto->nombre }}
                                        <div class="text-xs text-gray-400">{{ $reserva->deporte }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <div class="font-bold">
                                            {{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-gray-500">
                                            {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $clases = [
                                                'pendiente' => 'bg-yellow-100 text-yellow-800',
                                                'aprobada' => 'bg-green-100 text-green-800',
                                                'rechazada' => 'bg-red-100 text-red-800',
                                                'cancelada' => 'bg-gray-100 text-gray-800',
                                            ];
                                            $clase = $clases[$reserva->estado] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $clase }}">
                                            {{ ucfirst($reserva->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.reservas.show', $reserva) }}" class="text-indigo-600 hover:text-indigo-900">
                                            Ver Detalles
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No se encontraron reservas con los filtros seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $reservas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection