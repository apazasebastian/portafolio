@extends('layouts.app')

@section('title', 'Detalle de Reserva')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        
        <div class="mb-6">
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Dashboard
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            
            <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Reserva #{{ $reserva->id }}
                </h3>
                @php
                    $clases = [
                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                        'aprobada' => 'bg-green-100 text-green-800',
                        'rechazada' => 'bg-red-100 text-red-800',
                        'cancelada' => 'bg-gray-100 text-gray-800',
                    ];
                    $clase = $clases[$reserva->estado] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $clase }}">
                    {{ ucfirst($reserva->estado) }}
                </span>
            </div>

            <div class="px-6 py-5">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-8">
                    
                    <div class="col-span-2 md:col-span-1">
                        <h4 class="text-md font-bold text-gray-700 mb-4 border-b pb-2">Detalles del Evento</h4>
                        
                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Recinto</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $reserva->recinto->nombre }}</dd>
                        </div>

                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Actividad / Deporte</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $reserva->deporte }}</dd>
                        </div>

                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Fecha</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y') }}</dd>
                        </div>

                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Horario</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-bold bg-gray-50 p-2 rounded inline-block">
                                {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                            </dd>
                        </div>
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <h4 class="text-md font-bold text-gray-700 mb-4 border-b pb-2">Información del Solicitante</h4>

                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Nombre / Organización</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $reserva->nombre_organizacion ?? $reserva->representante_nombre }}
                            </dd>
                        </div>

                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">RUT</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $reserva->rut }}</dd>
                        </div>

                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $reserva->email }}</dd>
                        </div>

                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $reserva->telefono }}</dd>
                        </div>
                    </div>

                    <div class="col-span-2 border-t pt-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Observaciones del Usuario</dt>
                                <dd class="mt-1 text-sm text-gray-900 italic">
                                    {{ $reserva->observaciones ?? 'Sin observaciones' }}
                                </dd>
                            </div>
                            
                            @if($reserva->motivo_rechazo)
                            <div class="bg-red-50 p-3 rounded-md border border-red-200">
                                <dt class="text-sm font-bold text-red-700">Motivo del Rechazo</dt>
                                <dd class="mt-1 text-sm text-red-600">
                                    {{ $reserva->motivo_rechazo }}
                                </dd>
                            </div>
                            @endif

                            @if($reserva->aprobada_por)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gestionado por</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $reserva->aprobadaPor->name ?? 'Usuario Sistema' }}
                                </dd>
                            </div>
                            @endif
                        </div>
                    </div>
                </dl>
            </div>

            <!--  BOTONES SOLO PARA JEFE_RECINTOS (NO para encargado_recinto) -->
            @if($reserva->estado === 'pendiente' && auth()->user()->role !== 'encargado_recinto')
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                
                <button type="button" 
                        onclick="document.getElementById('form-rechazo').classList.toggle('hidden')" 
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition">
                    Rechazar
                </button>

                <form action="{{ route('admin.reservas.aprobar', $reserva) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition" 
                            onclick="return confirm('¿Estás seguro de aprobar esta reserva?')">
                        Aprobar Reserva
                    </button>
                </form>
            </div>
            
            <div id="form-rechazo" class="hidden bg-gray-100 px-6 py-4 border-t border-gray-200">
                <form action="{{ route('admin.reservas.rechazar', $reserva) }}" method="POST">
                    @csrf
                    <label for="motivo_rechazo" class="block text-sm font-medium text-gray-700 mb-2">
                        Motivo del rechazo:
                    </label>
                    <textarea name="motivo_rechazo" 
                              id="motivo_rechazo" 
                              rows="3" 
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" 
                              required 
                              placeholder="Explique por qué se rechaza la solicitud..."></textarea>
                    <div class="mt-3 flex justify-end space-x-2">
                        <button type="button" 
                                onclick="document.getElementById('form-rechazo').classList.add('hidden')"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition">
                            Confirmar Rechazo
                        </button>
                    </div>
                </form>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection