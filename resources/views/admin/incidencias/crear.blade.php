@extends('layouts.app')

@section('title', 'Reportar Incidencia')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        
        <!-- Breadcrumb -->
        <div class="mb-6">
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Dashboard
            </a>
        </div>

        <!-- Mensaje de Éxito -->
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
            <div class="flex items-center mb-4">
                <div class="p-3 bg-orange-100 rounded-full mr-4">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Reportar Incidencia</h1>
                    <p class="text-gray-600">Reserva #{{ $reserva->id }}</p>
                </div>
            </div>

            <!-- Información de la Reserva -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div>
                    <p class="text-sm text-gray-600">Recinto</p>
                    <p class="font-semibold text-gray-900">{{ $reserva->recinto->nombre }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Organización</p>
                    <p class="font-semibold text-gray-900">{{ $reserva->nombre_organizacion }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Fecha</p>
                    <p class="font-semibold text-gray-900">{{ $reserva->fecha_reserva->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Horario</p>
                    <p class="font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Formulario de Incidencia -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('admin.incidencias.store', ['reservaId' => $reserva->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Tipo de Incidencia -->
                <div class="mb-6">
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Incidencia <span class="text-red-500">*</span>
                    </label>
                    <select name="tipo" id="tipo" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 @error('tipo') border-red-500 @enderror">
                        <option value="">Seleccione un tipo</option>
                        <option value="problema_posuso" {{ old('tipo') == 'problema_posuso' ? 'selected' : '' }}>
                            Problema Post-Uso
                        </option>
                        <option value="dano" {{ old('tipo') == 'dano' ? 'selected' : '' }}>
                            Daño en Instalaciones
                        </option>
                        <option value="otro" {{ old('tipo') == 'otro' ? 'selected' : '' }}>
                            Otro
                        </option>
                    </select>
                    @error('tipo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NUEVO: Asistieron -->
                <div class="mb-6">
                    <label for="asistieron" class="block text-sm font-medium text-gray-700 mb-2">
                        ¿Asistieron? <span class="text-red-500">*</span>
                    </label>
                    <select name="asistieron" id="asistieron" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 @error('asistieron') border-red-500 @enderror">
                        <option value="">Seleccione una opción</option>
                        <option value="si" {{ old('asistieron') == 'si' ? 'selected' : '' }}>
                            Sí, Asistieron
                        </option>
                        <option value="no" {{ old('asistieron') == 'no' ? 'selected' : '' }}>
                            No, No Asistieron
                        </option>
                    </select>
                    @error('asistieron')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- CAMPOS CONDICIONALES: Mostrar si asistieron = SÍ -->
                <div id="camposAsistieron" class="space-y-6 hidden mb-6">
                    <!-- Estado del Recinto -->
                    <div>
                        <label for="estado_recinto" class="block text-sm font-medium text-gray-700 mb-2">
                            Estado del Recinto <span class="text-red-500">*</span>
                        </label>
                        <select name="estado_recinto" id="estado_recinto"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 @error('estado_recinto') border-red-500 @enderror">
                            <option value="">Seleccione un estado</option>
                            <option value="buen_estado" {{ old('estado_recinto') == 'buen_estado' ? 'selected' : '' }}>
                                Buen Estado
                            </option>
                            <option value="mal_estado" {{ old('estado_recinto') == 'mal_estado' ? 'selected' : '' }}>
                                Mal Estado
                            </option>
                        </select>
                        @error('estado_recinto')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cantidad de Personas -->
                    <div>
                        <label for="cantidad_personas" class="block text-sm font-medium text-gray-700 mb-2">
                            Cantidad de Personas que Asistieron <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="cantidad_personas" id="cantidad_personas" 
                               value="{{ old('cantidad_personas') }}" min="1" max="500"
                               placeholder="Ingrese la cantidad de personas"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 @error('cantidad_personas') border-red-500 @enderror">
                        @error('cantidad_personas')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Horas Reales -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Hora de Inicio Real -->
                        <div>
                            <label for="hora_inicio_real" class="block text-sm font-medium text-gray-700 mb-2">
                                Hora Real de Inicio <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="hora_inicio_real" id="hora_inicio_real" 
                                   value="{{ old('hora_inicio_real') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 @error('hora_inicio_real') border-red-500 @enderror">
                            @error('hora_inicio_real')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Hora prevista: {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }}</p>
                        </div>

                        <!-- Hora de Fin Real -->
                        <div>
                            <label for="hora_fin_real" class="block text-sm font-medium text-gray-700 mb-2">
                                Hora Real de Finalización <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="hora_fin_real" id="hora_fin_real" 
                                   value="{{ old('hora_fin_real') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 @error('hora_fin_real') border-red-500 @enderror">
                            @error('hora_fin_real')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Hora prevista: {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}</p>
                        </div>
                    </div>

                    <!-- Subida de Imágenes -->
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Imágenes de Evidencia (Opcional - Máximo 5)
                        </label>
                        <p class="text-xs text-gray-500 mb-3">
                            Puede adjuntar imágenes del estado del recinto después del uso (ej: daños, estado de limpieza, etc.)
                        </p>
                        
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-orange-400 transition-colors">
                            <input type="file" name="imagenes[]" id="imagenes" multiple 
                                   accept="image/jpeg,image/png,image/jpg,image/webp"
                                   class="hidden"
                                   onchange="previsualizarImagenes(this)">
                            <label for="imagenes" class="cursor-pointer">
                                <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm text-gray-600">Haz clic para seleccionar imágenes</span>
                                <p class="text-xs text-gray-400 mt-1">JPG, PNG o WebP (máx. 2MB cada una)</p>
                            </label>
                        </div>
                        
                        <!-- Vista previa de imágenes -->
                        <div id="previewImagenes" class="mt-4 grid grid-cols-5 gap-2 hidden"></div>
                        
                        <p id="contadorImagenes" class="mt-2 text-xs text-gray-500 hidden">
                            <span id="numImagenes">0</span>/5 imágenes seleccionadas
                        </p>
                        
                        @error('imagenes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('imagenes.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Descripción (siempre visible) -->
                <div class="mb-6">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción de la Incidencia <span class="text-red-500">*</span>
                    </label>
                    <textarea name="descripcion" id="descripcion" rows="6" required
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 @error('descripcion') border-red-500 @enderror"
                              placeholder="Describa detalladamente qué ocurrió, qué daños se observaron, etc.">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Sea lo más específico posible. Incluya detalles como ubicación exacta, magnitud del daño, etc.</p>
                </div>

                <!-- Información Adicional -->
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-blue-700">
                            <p class="font-medium mb-1">Información importante</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>La incidencia será registrada como "Reportada" inicialmente</li>
                                <li>Si selecciona "Sí, Asistieron", deberá completar Estado del Recinto, Cantidad de Personas y Horarios Reales</li>
                                <li>Si selecciona "No, No Asistieron", solo deberá completar la Descripción</li>
                                <li>Puede cambiar el estado posteriormente desde el panel de incidencias</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex gap-3">
                    <button type="submit" 
                            class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 px-4 rounded-md transition-colors shadow-sm">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Reportar Incidencia
                    </button>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-md transition-colors shadow-sm text-center">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Incidencias Previas -->
        @if($reserva->incidencias()->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Incidencias Reportadas</h3>
                
                <!-- Estadísticas por Tipo -->
                <div class="flex gap-4 text-sm">
                    @php
                        $totalIncidencias = $reserva->incidencias()->count();
                        $problemasPostUso = $reserva->incidencias()->where('tipo', 'problema_posuso')->count();
                        $danos = $reserva->incidencias()->where('tipo', 'dano')->count();
                        $otros = $reserva->incidencias()->where('tipo', 'otro')->count();
                    @endphp
                    
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600">Total:</span>
                        <span class="font-bold text-gray-900">{{ $totalIncidencias }}</span>
                    </div>
                    
                    @if($problemasPostUso > 0)
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600">Problema Post-Uso:</span>
                        <span class="font-bold text-orange-600">{{ $problemasPostUso }}</span>
                    </div>
                    @endif
                    
                    @if($danos > 0)
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600">Daño en Instalaciones:</span>
                        <span class="font-bold text-red-600">{{ $danos }}</span>
                    </div>
                    @endif
                    
                    @if($otros > 0)
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600">Otro:</span>
                        <span class="font-bold text-blue-600">{{ $otros }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="space-y-4">
                @foreach($reserva->incidencias as $incidencia)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                {{ $incidencia->estado === 'reportada' ? 'bg-yellow-100 text-yellow-800 border-yellow-400' : '' }}
                                {{ $incidencia->estado === 'en_revision' ? 'bg-blue-100 text-blue-800 border-blue-400' : '' }}
                                {{ $incidencia->estado === 'resuelta' ? 'bg-green-100 text-green-800 border-green-400' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $incidencia->estado)) }}
                            </span>
                            <span class="ml-2 text-xs text-gray-500">
                                {{ $incidencia->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <span class="text-xs font-medium px-2.5 py-0.5 rounded-full
                            {{ $incidencia->tipo === 'problema_posuso' ? 'bg-orange-100 text-orange-800' : '' }}
                            {{ $incidencia->tipo === 'dano' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $incidencia->tipo === 'otro' ? 'bg-blue-100 text-blue-800' : '' }}">
                            @if($incidencia->tipo === 'problema_posuso')
                                Problema Post-Uso
                            @elseif($incidencia->tipo === 'dano')
                                Daño en Instalaciones
                            @else
                                Otro
                            @endif
                        </span>
                    </div>
                    <p class="text-sm text-gray-700 mb-3">{{ $incidencia->descripcion }}</p>
                    
                    <!-- Botones de Cambio de Estado Rápido -->
                    <div class="flex items-center gap-2 pt-3 border-t border-gray-200">
                        <span class="text-xs font-medium text-gray-600 mr-2">Cambiar a:</span>
                        
                        @if($incidencia->estado !== 'reportada')
                            <form action="{{ route('admin.incidencias.cambiar-estado', $incidencia) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="estado" value="reportada">
                                <button type="submit" 
                                        class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-md transition-colors
                                               bg-yellow-100 text-yellow-800 hover:bg-yellow-200 border border-yellow-400">
                                    Reportada
                                </button>
                            </form>
                        @endif

                        @if($incidencia->estado !== 'en_revision')
                            <form action="{{ route('admin.incidencias.cambiar-estado', $incidencia) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="estado" value="en_revision">
                                <button type="submit" 
                                        class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-md transition-colors
                                               bg-blue-100 text-blue-800 hover:bg-blue-200 border border-blue-400">
                                    En Revisión
                                </button>
                            </form>
                        @endif

                        @if($incidencia->estado !== 'resuelta')
                            <form action="{{ route('admin.incidencias.cambiar-estado', $incidencia) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="estado" value="resuelta">
                                <button type="submit" 
                                        class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-md transition-colors
                                               bg-green-100 text-green-800 hover:bg-green-200 border border-green-400">
                                    Resuelta
                                </button>
                            </form>
                        @endif

                        <!-- Botón Ver Detalles -->
                        <a href="{{ route('admin.incidencias.show', $incidencia) }}" 
                           class="ml-auto inline-flex items-center px-3 py-1 text-xs font-medium rounded-md transition-colors
                                  bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-300">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Ver Detalles
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    // JAVASCRIPT: Mostrar/ocultar campos según "Asistieron"
    document.getElementById('asistieron').addEventListener('change', function() {
        const camposAsistieron = document.getElementById('camposAsistieron');
        const fieldsSi = ['estado_recinto', 'cantidad_personas', 'hora_inicio_real', 'hora_fin_real'];
        
        if (this.value === 'si') {
            // Mostrar campos de "Sí asistieron"
            camposAsistieron.classList.remove('hidden');
            // Hacer campos requeridos
            fieldsSi.forEach(field => {
                document.getElementById(field).required = true;
            });
        } else {
            // Ocultar campos
            camposAsistieron.classList.add('hidden');
            // Hacer campos no requeridos y limpiar
            fieldsSi.forEach(field => {
                document.getElementById(field).required = false;
                document.getElementById(field).value = '';
            });
            // Limpiar imágenes si cambió de opción
            if (typeof limpiarImagenes === 'function') {
                limpiarImagenes();
            }
        }
    });

    // Almacén de archivos acumulados
    let archivosAcumulados = [];

    // Previsualización de imágenes con acumulación
    function previsualizarImagenes(input) {
        const previewContainer = document.getElementById('previewImagenes');
        const contador = document.getElementById('contadorImagenes');
        const numImagenes = document.getElementById('numImagenes');
        
        if (input.files && input.files.length > 0) {
            // Agregar nuevos archivos al array acumulado
            const nuevosArchivos = Array.from(input.files);
            
            // Validar que no exceda 5 en total
            if (archivosAcumulados.length + nuevosArchivos.length > 5) {
                alert(`Solo puede seleccionar un máximo de 5 imágenes. Ya tiene ${archivosAcumulados.length} imagen(es).`);
                input.value = '';
                return;
            }
            
            // Validar tamaño de nuevos archivos
            for (const file of nuevosArchivos) {
                if (file.size > 2 * 1024 * 1024) {
                    alert(`La imagen "${file.name}" excede el límite de 2MB`);
                    input.value = '';
                    return;
                }
            }
            
            // Agregar archivos válidos
            archivosAcumulados = [...archivosAcumulados, ...nuevosArchivos];
            
            // Actualizar el input con todos los archivos usando DataTransfer
            actualizarInputArchivos(input);
            renderizarPreviews();
        }
    }
    
    function actualizarInputArchivos(input) {
        const dataTransfer = new DataTransfer();
        archivosAcumulados.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;
    }
    
    function renderizarPreviews() {
        const previewContainer = document.getElementById('previewImagenes');
        const contador = document.getElementById('contadorImagenes');
        const numImagenes = document.getElementById('numImagenes');
        
        previewContainer.innerHTML = '';
        
        if (archivosAcumulados.length > 0) {
            previewContainer.classList.remove('hidden');
            contador.classList.remove('hidden');
            numImagenes.textContent = archivosAcumulados.length;
            
            archivosAcumulados.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative aspect-square group';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-full object-cover rounded border border-gray-300';
                    img.alt = 'Imagen ' + (index + 1);
                    
                    const badge = document.createElement('span');
                    badge.className = 'absolute top-1 left-1 bg-gray-800 text-white text-xs px-1.5 py-0.5 rounded';
                    badge.textContent = index + 1;
                    
                    // Botón para eliminar imagen
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity';
                    removeBtn.innerHTML = '×';
                    removeBtn.onclick = function(e) {
                        e.preventDefault();
                        eliminarImagen(index);
                    };
                    
                    wrapper.appendChild(img);
                    wrapper.appendChild(badge);
                    wrapper.appendChild(removeBtn);
                    previewContainer.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });
        } else {
            previewContainer.classList.add('hidden');
            contador.classList.add('hidden');
        }
    }
    
    function eliminarImagen(index) {
        archivosAcumulados.splice(index, 1);
        const input = document.getElementById('imagenes');
        actualizarInputArchivos(input);
        renderizarPreviews();
    }
    
    // Limpiar archivos cuando cambia la opción de asistieron
    function limpiarImagenes() {
        archivosAcumulados = [];
        const input = document.getElementById('imagenes');
        if (input) {
            input.value = '';
            document.getElementById('previewImagenes').innerHTML = '';
            document.getElementById('previewImagenes').classList.add('hidden');
            document.getElementById('contadorImagenes').classList.add('hidden');
        }
    }
</script>

@endsection