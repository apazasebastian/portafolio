@extends('layouts.app')

@section('title', 'Crear Evento - Panel Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('admin.eventos.index') }}" 
               class="text-blue-600 hover:text-blue-800 inline-flex items-center mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Eventos
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Crear Nuevo Evento</h1>
            <p class="text-gray-600 mt-2">Completa los datos del evento para el carrusel</p>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <form action="{{ route('admin.eventos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Título -->
                <div class="mb-6">
                    <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">
                        Título del Evento <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="titulo" 
                           id="titulo" 
                           value="{{ old('titulo') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('titulo') border-red-500 @enderror"
                           placeholder="Ej: Clases de Natación Gratuitas">
                    @error('titulo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="mb-6">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción <span class="text-red-500">*</span>
                    </label>
                    <textarea name="descripcion" 
                              id="descripcion" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('descripcion') border-red-500 @enderror"
                              placeholder="Describe brevemente el evento...">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Máximo 500 caracteres</p>
                </div>

                <!-- Fecha del Evento -->
                <div class="mb-6">
                    <label for="fecha_evento" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha del Evento <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="fecha_evento" 
                           id="fecha_evento" 
                           value="{{ old('fecha_evento') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha_evento') border-red-500 @enderror">
                    @error('fecha_evento')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Enlace Externo -->
                <div class="mb-6">
                    <label for="enlace_externo" class="block text-sm font-medium text-gray-700 mb-2">
                        Enlace para Más Información (Opcional)
                    </label>
                    <input type="url" 
                           name="enlace_externo" 
                           id="enlace_externo" 
                           value="{{ old('enlace_externo') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('enlace_externo') border-red-500 @enderror"
                           placeholder="https://ejemplo.com">
                    @error('enlace_externo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Imagen -->
                <div class="mb-6">
                    <label for="imagen" class="block text-sm font-medium text-gray-700 mb-2">
                        Imagen del Evento <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           name="imagen" 
                           id="imagen" 
                           accept="image/jpeg,image/png,image/jpg"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('imagen') border-red-500 @enderror">
                    @error('imagen')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Formatos: JPG, JPEG, PNG. Máximo 2MB. Tamaño recomendado: 1200x500px</p>
                    
                    <!-- Preview -->
                    <div id="preview" class="mt-4 hidden">
                        <p class="text-sm font-medium text-gray-700 mb-2">Vista Previa:</p>
                        <img id="preview-image" src="" alt="Preview" class="max-w-full h-48 object-cover rounded-lg border">
                    </div>
                </div>

                <!-- Estado -->
                <div class="mb-6">
                    <label for="activo" class="block text-sm font-medium text-gray-700 mb-2">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select name="activo" 
                            id="activo" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="1" {{ old('activo', '1') == '1' ? 'selected' : '' }}>Activo (visible en el carrusel)</option>
                        <option value="0" {{ old('activo') == '0' ? 'selected' : '' }}>Inactivo (oculto)</option>
                    </select>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('admin.eventos.index') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Crear Evento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Preview de imagen
document.getElementById('imagen').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').classList.remove('hidden');
            document.getElementById('preview-image').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection