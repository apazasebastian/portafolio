@extends('layouts.app')

@section('title', 'Solicitar Reserva - ' . $recinto->nombre)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        
        <!-- Breadcrumb -->
        <div class="mb-6">
            <a href="{{ route('calendario') }}" class="text-blue-600 hover:text-blue-800">
                ← Volver al calendario
            </a>
        </div>

        <!-- Header con progreso -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-800">Solicitar Reserva</h1>
                <div class="text-sm text-gray-500">Paso 1 de 4</div>
            </div>
            
            <!-- Barra de progreso -->
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: 25%"></div>
            </div>
        </div>

        <!-- Info del Recinto -->
        @php
            // Decodificar JSON si es necesario
            $horarios = is_array($recinto->horarios_disponibles) 
                ? $recinto->horarios_disponibles 
                : json_decode($recinto->horarios_disponibles, true);
                
            $diasCerrados = is_array($recinto->dias_cerrados) 
                ? $recinto->dias_cerrados 
                : ($recinto->dias_cerrados ? json_decode($recinto->dias_cerrados, true) : null);
        @endphp
        
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 mb-6 text-white">
            <h2 class="text-2xl font-bold mb-3">{{ $recinto->nombre }}</h2>
            <p class="mb-4 opacity-90">{{ $recinto->descripcion }}</p>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                    <span>Capacidad: <strong>{{ $recinto->capacidad_maxima }} personas</strong></span>
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <span>Horario: <strong>{{ $horarios['inicio'] ?? '08:00' }} - {{ $horarios['fin'] ?? '23:00' }}</strong></span>
                </div>
            </div>
            
            @if($diasCerrados && in_array('monday', $diasCerrados))
            <div class="mt-4 bg-red-500 bg-opacity-20 border border-white border-opacity-30 rounded-lg p-3">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span><strong>Importante:</strong> Cerrado los lunes por mantenimiento</span>
                </div>
            </div>
            @endif
        </div>

        <form method="POST" action="{{ route('reservas.store') }}" id="reservaForm">
            @csrf
            <input type="hidden" name="recinto_id" value="{{ $recinto->id }}">

            <!-- PASO 1: Tipo de Actividad -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 rounded-full w-8 h-8 flex items-center justify-center text-blue-600 font-bold mr-3">
                        1
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Tipo de Actividad</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Deporte a Practicar
                    </label>
                    <select name="deporte" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccione un deporte</option>
                        <option value="Fútbol" {{ old('deporte') == 'Fútbol' ? 'selected' : '' }}>Fútbol</option>
                        <option value="Fútsal" {{ old('deporte') == 'Fútsal' ? 'selected' : '' }}>Fútsal</option>
                        <option value="Básquetbol" {{ old('deporte') == 'Básquetbol' ? 'selected' : '' }}>Básquetbol</option>
                        <option value="Vóleibol" {{ old('deporte') == 'Vóleibol' ? 'selected' : '' }}>Vóleibol</option>
                        <option value="Handball" {{ old('deporte') == 'Handball' ? 'selected' : '' }}>Handball</option>
                        <option value="Natación" {{ old('deporte') == 'Natación' ? 'selected' : '' }}>Natación</option>
                        <option value="Actividad Recreativa" {{ old('deporte') == 'Actividad Recreativa' ? 'selected' : '' }}>Actividad Recreativa</option>
                        <option value="Otro" {{ old('deporte') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('deporte')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- PASO 2: Datos de la Organización -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 rounded-full w-8 h-8 flex items-center justify-center text-blue-600 font-bold mr-3">
                        2
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Datos de la Organización</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Nombre del Club/Organización
                        </label>
                        <input type="text" name="nombre_organizacion" 
                               value="{{ old('nombre_organizacion') }}" 
                               required
                               placeholder="Ej: Club Deportivo Los Campeones"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('nombre_organizacion')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Nombre del Representante
                        </label>
                        <input type="text" name="representante_nombre" 
                               value="{{ old('representante_nombre') }}" 
                               required
                               placeholder="Ej: Juan Pérez González"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('representante_nombre')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> RUT del Representante
                        </label>
                        <input type="text" 
                               id="rut" 
                               name="rut" 
                               value="{{ old('rut') }}" 
                               required
                               placeholder="12345678-9"
                               maxlength="12"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">El guión se agregará automáticamente</p>
                        @error('rut')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Teléfono de Contacto
                        </label>
                        <input type="tel" name="telefono" 
                               value="{{ old('telefono') }}" 
                               placeholder="+56 9 8765 4321"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('telefono')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Correo Electrónico
                        </label>
                        <input type="email" name="email" 
                               value="{{ old('email') }}" 
                               required
                               placeholder="ejemplo@correo.com"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Confirmar Correo
                        </label>
                        <input type="email" name="email_confirmacion" 
                               value="{{ old('email_confirmacion') }}" 
                               required
                               placeholder="ejemplo@correo.com"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('email_confirmacion')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Dirección de la Organización
                        </label>
                        <input type="text" name="direccion" 
                               value="{{ old('direccion') }}" 
                               placeholder="Calle, número"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Región
                        </label>
                        <select id="region" name="region"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccione región</option>
                        </select>
                        @error('region')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Comuna
                        </label>
                        <select id="comuna" name="comuna" disabled
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100">
                            <option value="">Seleccione comuna</option>
                        </select>
                        @error('comuna')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- PASO 3: Detalles de la Reserva -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 rounded-full w-8 h-8 flex items-center justify-center text-blue-600 font-bold mr-3">
                        3
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Detalles de la Reserva</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Fecha
                        </label>
                        <input type="date" name="fecha_reserva" 
                               value="{{ old('fecha_reserva') }}" 
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                               required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('fecha_reserva')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Hora de Inicio
                        </label>
                        <select name="hora_inicio" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccionar</option>
                            @for($h = 8; $h < 23; $h++)
                                <option value="{{ sprintf('%02d:00', $h) }}">{{ sprintf('%02d:00', $h) }}</option>
                            @endfor
                        </select>
                        @error('hora_inicio')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Hora de Término
                        </label>
                        <select name="hora_fin" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccionar</option>
                            @for($h = 9; $h <= 23; $h++)
                                <option value="{{ sprintf('%02d:00', $h) }}">{{ sprintf('%02d:00', $h) }}</option>
                            @endfor
                        </select>
                        @error('hora_fin')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Cantidad de Personas
                    </label>
                    <div class="relative">
                        <input type="number" name="cantidad_personas" 
                               value="{{ old('cantidad_personas') }}" 
                               min="1" 
                               max="{{ $recinto->capacidad_maxima }}" 
                               required
                               placeholder="Número de asistentes"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <span class="absolute right-4 top-3 text-gray-500 text-sm">
                            Máx: {{ $recinto->capacidad_maxima }}
                        </span>
                    </div>
                    @error('cantidad_personas')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones Adicionales
                    </label>
                    <textarea name="observaciones" rows="4" 
                              placeholder="Describa el tipo de actividad, equipamiento necesario, etc."
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('observaciones') }}</textarea>
                </div>
            </div>

            <!-- PASO 4: Confirmación -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 rounded-full w-8 h-8 flex items-center justify-center text-blue-600 font-bold mr-3">
                        4
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Confirmación</h3>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-yellow-700">
                            <p class="font-medium">Importante:</p>
                            <p>Su reserva estará sujeta a aprobación. Recibirá una notificación por correo electrónico.</p>
                        </div>
                    </div>
                </div>

                <label class="flex items-start cursor-pointer">
                    <input type="checkbox" name="acepta_reglamento" value="1" required
                           class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-3 text-sm text-gray-700">
                        <span class="text-red-500">*</span> Acepto el 
                        <button type="button" onclick="abrirReglamento()" class="text-blue-600 hover:underline font-medium">
                            reglamento de uso
                        </button> 
                        de recintos deportivos municipales
                    </span>
                </label>
                @error('acepta_reglamento')
                    <p class="text-red-600 text-sm mt-1 ml-7">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex justify-between">
                <a href="{{ route('calendario') }}" 
                   class="px-8 py-3 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition-colors">
                    Cancelar
                </a>
                
                <button type="submit" 
                        class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors shadow-lg hover:shadow-xl">
                    Enviar Solicitud
                </button>
            </div>
        </form>

    </div>
</div>

<!-- Modal del Reglamento -->
<div id="modalReglamento" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden shadow-2xl">
        
        <!-- Header del Modal -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-2xl font-bold mb-2">Reglamento de Uso</h3>
                    <p class="text-blue-100 text-sm">Recintos Deportivos Municipales</p>
                </div>
                <button onclick="cerrarReglamento()" type="button" 
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Contenido del Modal -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
            
            <!-- Introducción -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <p class="text-sm text-gray-700">
                    Al solicitar una reserva, usted acepta cumplir con las siguientes condiciones establecidas 
                    por la Municipalidad de Arica para el uso de recintos deportivos.
                </p>
            </div>

            <!-- Lista de Condiciones -->
            <div class="space-y-4">
                
                <!-- Condición 1 -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-1">Uso exclusivo para actividades deportivas y recreativas</h4>
                        <p class="text-sm text-gray-600">
                            Los recintos están destinados únicamente para prácticas deportivas, 
                            entrenamiento y actividades recreativas sanas.
                        </p>
                    </div>
                </div>

                <!-- Condición 2 -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-1">Mantener el orden y limpieza del recinto</h4>
                        <p class="text-sm text-gray-600">
                            Los usuarios deben dejar el espacio limpio y ordenado al finalizar su uso. 
                            Se debe hacer uso responsable del mobiliario y las instalaciones.
                        </p>
                    </div>
                </div>

                <!-- Condición 3 -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-1">Respetar horarios asignados</h4>
                        <p class="text-sm text-gray-600">
                            Debe presentarse 15 minutos antes del horario reservado y desocupar 
                            puntualmente al finalizar el tiempo asignado.
                        </p>
                    </div>
                </div>

                <!-- Condición 4 -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-1">La reserva está sujeta a aprobación municipal</h4>
                        <p class="text-sm text-gray-600">
                            Todas las solicitudes serán revisadas por personal municipal. 
                            Recibirá confirmación por correo electrónico.
                        </p>
                    </div>
                </div>

                <!-- Condición 5 -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-1">Prohibido el consumo de alcohol y sustancias prohibidas</h4>
                        <p class="text-sm text-gray-600">
                            Está terminantemente prohibido el consumo de bebidas alcohólicas, 
                            drogas o cualquier sustancia prohibida dentro de las instalaciones.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Nota Final -->
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-700">
                    <strong>Nota importante:</strong> El incumplimiento de estas normas puede resultar en 
                    la cancelación inmediata de la reserva y la suspensión de futuros usos de los recintos deportivos municipales.
                </p>
            </div>

        </div>

        <!-- Footer del Modal -->
        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
            <button type="button" onclick="cerrarReglamento()" 
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                Entendido
            </button>
        </div>

    </div>
</div>

<script>
function abrirReglamento() {
    document.getElementById('modalReglamento').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function cerrarReglamento() {
    document.getElementById('modalReglamento').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

document.getElementById('modalReglamento')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarReglamento();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarReglamento();
    }
});
</script>

@endsection