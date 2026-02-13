@extends('layouts.app')

@section('title', 'Reportar Incidencia')

@section('content')
<div class="max-w-4xl mx-auto px-6 lg:px-8 py-8">

    <!-- Header Institucional -->
    <div class="mb-8">
        <h1 class="text-3xl font-serif font-bold text-gray-900">PORTAL MUNICIPAL DE DEPORTES</h1>
        <p class="text-gray-500 mt-1">Gestión de Incidencias Administrativas</p>
        <div class="flex items-center justify-between mt-4">
            <hr class="border-gray-200 flex-1">
            <span class="ml-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">
                EXPEDIENTE: {{ date('Y') }}-INC-{{ str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) }}
            </span>
        </div>
    </div>

    <!-- Mensaje de Error -->
    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
            <p class="text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
            <p class="text-red-700 font-semibold mb-2">Por favor corrija los siguientes errores:</p>
            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.incidencias.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- ============================================================ -->
        <!-- SECCIÓN 1: Detalles de la Reserva (auto-completados por JS)  -->
        <!-- ============================================================ -->
        <div class="mb-8">
            <h2 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-4">Detalles de la Reserva</h2>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Recinto</p>
                        <p id="detalle-recinto" class="text-sm font-semibold text-gray-900">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Organización</p>
                        <p id="detalle-organizacion" class="text-sm font-semibold text-gray-900">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Fecha</p>
                        <p id="detalle-fecha" class="text-sm font-semibold text-gray-900">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Horario</p>
                        <p id="detalle-horario" class="text-sm font-semibold text-gray-900">—</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- SECCIÓN 2: Formulario de Reporte                             -->
        <!-- ============================================================ -->
        <div class="mb-8">
            <h2 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-4">Formulario de Reporte</h2>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <!-- Fila 1: Tipo de Incidencia + Eventos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Tipo de Incidencia -->
                    <div>
                        <label for="tipo" class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">
                            Tipo de Incidencia
                        </label>
                        <select name="tipo" id="tipo" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm @error('tipo') border-red-500 @enderror">
                            <option value="">Seleccione una opción...</option>
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

                    <!-- Eventos (reemplaza ¿Asistieron?) -->
                    <div>
                        <label for="reserva_id" class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">
                            Eventos
                        </label>
                        <select name="reserva_id" id="reserva_id" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm @error('reserva_id') border-red-500 @enderror">
                            <option value="">Seleccione un evento...</option>
                            @foreach($reservas as $reserva)
                                <option value="{{ $reserva->id }}" 
                                        data-recinto="{{ $reserva->recinto->nombre }}"
                                        data-organizacion="{{ $reserva->nombre_organizacion }}"
                                        data-fecha="{{ $reserva->fecha_reserva->format('d/m/Y') }}"
                                        data-hora-inicio="{{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }}"
                                        data-hora-fin="{{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}"
                                        {{ $selectedReservaId == $reserva->id ? 'selected' : '' }}>
                                    {{ $reserva->recinto->nombre }} — {{ $reserva->nombre_organizacion }} ({{ $reserva->fecha_reserva->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('reserva_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Descripción -->
                <div class="mb-6">
                    <label for="descripcion" class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">
                        Descripción de la Incidencia
                    </label>
                    <textarea name="descripcion" id="descripcion" rows="5" required
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm @error('descripcion') border-red-500 @enderror"
                              placeholder="Detalle lo ocurrido durante el uso del recinto de forma clara y objetiva...">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fila 2: Estado del Recinto + Cantidad de Personas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="estado_recinto" class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">
                            Estado del Recinto
                        </label>
                        <select name="estado_recinto" id="estado_recinto"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm @error('estado_recinto') border-red-500 @enderror">
                            <option value="">Seleccione un estado...</option>
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
                    <div>
                        <label for="cantidad_personas" class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">
                            Cantidad de Personas
                        </label>
                        <input type="number" name="cantidad_personas" id="cantidad_personas" 
                               value="{{ old('cantidad_personas') }}" min="1" max="500"
                               placeholder="Ej: 25"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm @error('cantidad_personas') border-red-500 @enderror">
                        @error('cantidad_personas')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Fila 3: Horas Reales -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="hora_inicio_real" class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">
                            Hora Real de Inicio
                        </label>
                        <input type="time" name="hora_inicio_real" id="hora_inicio_real" 
                               value="{{ old('hora_inicio_real') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm @error('hora_inicio_real') border-red-500 @enderror">
                        @error('hora_inicio_real')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="hora_fin_real" class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">
                            Hora Real de Finalización
                        </label>
                        <input type="time" name="hora_fin_real" id="hora_fin_real" 
                               value="{{ old('hora_fin_real') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm @error('hora_fin_real') border-red-500 @enderror">
                        @error('hora_fin_real')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Subida de Imágenes -->
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">
                        Imágenes de Evidencia (Opcional — Máximo 5)
                    </label>
                    <p class="text-xs text-gray-500 mb-3">
                        Adjunte fotografías del estado del recinto (ej: daños, estado de limpieza, etc.)
                    </p>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors">
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
        </div>

        <!-- ============================================================ -->
        <!-- Información Importante                                       -->
        <!-- ============================================================ -->
        <div class="mb-8 border-l-4 border-gray-800 bg-gray-50 rounded-r-xl p-5">
            <p class="font-black text-xs text-gray-700 uppercase tracking-widest mb-2">Información Importante</p>
            <p class="text-sm text-gray-600 leading-relaxed">
                Las incidencias reportadas serán revisadas por la administración municipal para garantizar el correcto uso de las 
                instalaciones. Este reporte tiene carácter de declaración oficial; por favor, asegúrese de que los datos proporcionados 
                sean veraces. El equipo administrativo se pondrá en contacto con usted en un plazo máximo de 48 horas hábiles si 
                requiere mayor información.
            </p>
        </div>

        <!-- ============================================================ -->
        <!-- Botones de Acción                                            -->
        <!-- ============================================================ -->
        <hr class="border-gray-200 mb-6">
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('admin.incidencias.index') }}" 
               class="px-8 py-3 border-2 border-gray-300 text-gray-600 font-bold text-sm uppercase tracking-widest text-center rounded hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-8 py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm uppercase tracking-widest text-center rounded transition-colors shadow-sm">
                Reportar Incidencia
            </button>
        </div>
    </form>

    <!-- Footer Institucional -->
    <div class="mt-12 text-center">
        <p class="text-xs font-bold text-gray-300 uppercase tracking-[0.3em]">Departamento de Deportes — Ilustre Municipalidad</p>
    </div>
</div>

<script>
    /**
     * Auto-completar detalles de reserva al seleccionar un evento
     */
    const selectEvento = document.getElementById('reserva_id');
    
    function actualizarDetalles() {
        const option = selectEvento.options[selectEvento.selectedIndex];
        
        if (option && option.value) {
            document.getElementById('detalle-recinto').textContent = option.dataset.recinto || '—';
            document.getElementById('detalle-organizacion').textContent = option.dataset.organizacion || '—';
            document.getElementById('detalle-fecha').textContent = option.dataset.fecha || '—';
            document.getElementById('detalle-horario').textContent = 
                (option.dataset.horaInicio && option.dataset.horaFin) 
                    ? option.dataset.horaInicio + ' - ' + option.dataset.horaFin 
                    : '—';
        } else {
            document.getElementById('detalle-recinto').textContent = '—';
            document.getElementById('detalle-organizacion').textContent = '—';
            document.getElementById('detalle-fecha').textContent = '—';
            document.getElementById('detalle-horario').textContent = '—';
        }
    }

    selectEvento.addEventListener('change', actualizarDetalles);

    // Si viene pre-seleccionado, llenar los detalles al cargar
    if (selectEvento.value) {
        actualizarDetalles();
    }

    /**
     * Vista previa de imágenes seleccionadas
     */
    function previsualizarImagenes(input) {
        const preview = document.getElementById('previewImagenes');
        const contador = document.getElementById('contadorImagenes');
        const numImagenes = document.getElementById('numImagenes');
        
        preview.innerHTML = '';
        
        if (input.files && input.files.length > 0) {
            if (input.files.length > 5) {
                alert('Solo puede seleccionar un máximo de 5 imágenes');
                input.value = '';
                preview.classList.add('hidden');
                contador.classList.add('hidden');
                return;
            }
            
            preview.classList.remove('hidden');
            contador.classList.remove('hidden');
            numImagenes.textContent = input.files.length;
            
            for (let i = 0; i < input.files.length; i++) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative aspect-square rounded-lg overflow-hidden border border-gray-200';
                    div.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover" alt="Preview">';
                    preview.appendChild(div);
                };
                reader.readAsDataURL(input.files[i]);
            }
        } else {
            preview.classList.add('hidden');
            contador.classList.add('hidden');
        }
    }
</script>
@endsection