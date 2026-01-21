@extends('layouts.app')

@section('title', 'Crear Nuevo Recinto')

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <a href="{{ route('admin.recintos.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
            ← Volver a Recintos
        </a>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Crear Nuevo Recinto</h1>
        <p class="text-gray-600">Completa la información del recinto deportivo</p>
    </div>

    <!-- Formulario -->
    <form method="POST" action="{{ route('admin.recintos.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Columna Principal -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Card: Información Básica -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Información Básica</h3>
                    
                    <div class="space-y-4">
                        <!-- Nombre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre del Recinto <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nombre" 
                                   value="{{ old('nombre') }}"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('nombre') border-red-500 @enderror"
                                   placeholder="Ej: Epicentro 1"
                                   required>
                            @error('nombre')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Descripción
                            </label>
                            <textarea name="descripcion" 
                                      rows="3"
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('descripcion') border-red-500 @enderror"
                                      placeholder="Descripción breve del recinto">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Capacidad -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Capacidad Máxima <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="capacidad_maxima" 
                                   value="{{ old('capacidad_maxima', 100) }}"
                                   min="1"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('capacidad_maxima') border-red-500 @enderror"
                                   placeholder="100"
                                   required>
                            <p class="text-xs text-gray-500 mt-1">Número máximo de personas permitidas</p>
                            @error('capacidad_maxima')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Card: Horarios -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Horarios de Funcionamiento</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Hora Inicio -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Hora de Inicio <span class="text-red-500">*</span>
                            </label>
                            <input type="time" 
                                   name="horario_inicio" 
                                   value="{{ old('horario_inicio', '08:00') }}"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('horario_inicio') border-red-500 @enderror"
                                   required>
                            @error('horario_inicio')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hora Fin -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Hora de Fin <span class="text-red-500">*</span>
                            </label>
                            <input type="time" 
                                   name="horario_fin" 
                                   value="{{ old('horario_fin', '23:00') }}"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('horario_fin') border-red-500 @enderror"
                                   required>
                            @error('horario_fin')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Días Cerrados -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Días Cerrados (opcional)
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @php
                                $dias = [
                                    'monday' => 'Lunes',
                                    'tuesday' => 'Martes',
                                    'wednesday' => 'Miércoles',
                                    'thursday' => 'Jueves',
                                    'friday' => 'Viernes',
                                    'saturday' => 'Sábado',
                                    'sunday' => 'Domingo'
                                ];
                            @endphp
                            @foreach($dias as $valor => $nombre)
                                <label class="flex items-center space-x-2 cursor-pointer p-2 rounded hover:bg-gray-50">
                                    <input type="checkbox" 
                                           name="dias_cerrados[]" 
                                           value="{{ $valor }}"
                                           {{ in_array($valor, old('dias_cerrados', [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">{{ $nombre }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Selecciona los días en que el recinto permanecerá cerrado</p>
                    </div>
                </div>

                <!-- Card: Imagen -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Imagen del Recinto</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Subir Imagen (opcional)
                        </label>
                        <input type="file" 
                               name="imagen" 
                               accept="image/jpeg,image/png,image/jpg"
                               id="imagenInput"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('imagen') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-2">Formatos permitidos: JPG, JPEG, PNG. Tamaño máximo: 2MB</p>
                        @error('imagen')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preview de imagen -->
                    <div id="imagePreview" class="hidden mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vista Previa</label>
                        <img id="preview" src="" alt="Preview" class="w-full max-w-md h-48 object-cover rounded-lg border border-gray-200">
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                
                <!-- Card: Estado -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Estado del Recinto</h3>
                    
                    <div class="space-y-2">
                        <label class="flex items-center space-x-3 cursor-pointer p-3 rounded-lg hover:bg-gray-50 transition border-2 border-transparent has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                            <input type="radio" 
                                   name="activo" 
                                   value="1" 
                                   {{ old('activo', '1') == '1' ? 'checked' : '' }}
                                   class="text-green-600 focus:ring-green-500">
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-gray-900">Activo</div>
                                <div class="text-xs text-gray-500">El recinto está disponible para reservas</div>
                            </div>
                        </label>

                        <label class="flex items-center space-x-3 cursor-pointer p-3 rounded-lg hover:bg-gray-50 transition border-2 border-transparent has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                            <input type="radio" 
                                   name="activo" 
                                   value="0" 
                                   {{ old('activo') == '0' ? 'checked' : '' }}
                                   class="text-red-600 focus:ring-red-500">
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-gray-900">Inactivo</div>
                                <div class="text-xs text-gray-500">El recinto no está disponible</div>
                            </div>
                        </label>
                    </div>
                    @error('activo')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Card: Información Importante -->
                <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        Información
                    </h4>
                    <ul class="space-y-1 text-xs text-blue-800">
                        <li>• Completa todos los campos obligatorios (*)</li>
                        <li>• El horario define cuándo se puede reservar</li>
                        <li>• Los días cerrados bloquean reservas automáticamente</li>
                        <li>• La imagen es opcional pero recomendada</li>
                    </ul>
                </div>

                <!-- Card: Acciones -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="space-y-3">
                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors shadow-md hover:shadow-lg flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Crear Recinto
                        </button>
                        <a href="{{ route('admin.recintos.index') }}" 
                           class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-4 rounded-lg transition-colors">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript para preview de imagen -->
<script>
document.getElementById('imagenInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('imagePreview').classList.add('hidden');
    }
});
</script>
@endsection