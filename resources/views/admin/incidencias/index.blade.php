@extends('layouts.app')

@section('title', 'Reporte Diario - Sistema de Reservas')

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-8">

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-serif font-bold text-gray-900">Reporte Diario</h1>
        <p class="text-gray-500 mt-1">Gestión institucional de incidencias y novedades en recintos deportivos.</p>
    </div>

    <hr class="border-gray-200 mb-8">

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

    <!-- ================================================================ -->
    <!-- SECCIÓN 1: Estado de Instalaciones                               -->
    <!-- ================================================================ -->
    <div class="mb-12">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Estado de Instalaciones</h2>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($recintos as $recinto)
                <div class="border border-gray-200 rounded-lg overflow-hidden flex flex-col" data-recinto-card="{{ $recinto->id }}">
                    <!-- Imagen del Recinto -->
                    <div class="h-40 bg-gray-200 overflow-hidden">
                        @if($recinto->imagen_url)
                            <img src="{{ asset('storage/' . $recinto->imagen_url) }}" 
                                 alt="{{ $recinto->nombre }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.src='https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=400'">
                        @else
                            <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Contenido -->
                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="font-bold text-gray-900 mb-1">{{ $recinto->nombre }}</h3>
                        <p class="text-xs text-gray-500 mb-4 line-clamp-2">{{ $recinto->descripcion ?? 'Sin descripción disponible.' }}</p>

                        <!-- Toggle: ¿Hubo una incidencia? -->
                        <div class="mt-auto">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">¿HUBO UNA INCIDENCIA?</p>
                            <div class="flex border border-gray-300 rounded overflow-hidden mb-3">
                                <button type="button" 
                                        class="incidencia-toggle flex-1 py-2 text-sm font-semibold text-center transition-colors bg-gray-100 text-gray-600"
                                        data-recinto="{{ $recinto->id }}" data-value="no"
                                        onclick="toggleIncidencia(this, 'no')">
                                    No
                                </button>
                                <button type="button" 
                                        class="incidencia-toggle flex-1 py-2 text-sm font-semibold text-center transition-colors bg-white text-gray-400"
                                        data-recinto="{{ $recinto->id }}" data-value="si"
                                        onclick="toggleIncidencia(this, 'si')">
                                    Sí
                                </button>
                            </div>

                            <!-- Botón ENVIAR (default) -->
                            <div class="btn-enviar-wrapper" data-recinto="{{ $recinto->id }}">
                                <button type="button" 
                                        class="w-full py-3 text-sm font-bold uppercase tracking-wider text-white bg-gray-800 hover:bg-gray-700 transition-colors"
                                        onclick="enviarSinIncidencia({{ $recinto->id }})">
                                    ENVIAR
                                </button>
                            </div>

                            <!-- Botón REPORTAR INFORME (cuando Sí) -->
                            <div class="btn-reportar-wrapper hidden" data-recinto="{{ $recinto->id }}">
                                <a href="{{ route('admin.incidencias.crear') }}?recinto_id={{ $recinto->id }}" 
                                   class="block w-full py-3 text-sm font-bold uppercase tracking-wider text-center text-white bg-orange-500 hover:bg-orange-600 transition-colors">
                                    REPORTAR INFORME
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ================================================================ -->
    <!-- SECCIÓN 2: Historial de Reportes                                 -->
    <!-- ================================================================ -->
    <div>
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Historial de Reportes</h2>
                <p class="text-sm text-gray-500">Consulta los registros previos y el estado de las resoluciones.</p>
            </div>

            <!-- Filtros -->
            <form method="GET" action="{{ route('admin.incidencias.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Fecha</label>
                    <input type="date" name="fecha" value="{{ request('fecha') }}"
                           class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Recinto</label>
                    <select name="recinto_id" class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos los recintos</option>
                        @foreach($recintos as $recinto)
                            <option value="{{ $recinto->id }}" {{ request('recinto_id') == $recinto->id ? 'selected' : '' }}>
                                {{ $recinto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Estado</label>
                    <select name="estado" class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos</option>
                        <option value="reportada" {{ request('estado') == 'reportada' ? 'selected' : '' }}>Reportada</option>
                        <option value="en_revision" {{ request('estado') == 'en_revision' ? 'selected' : '' }}>En Revisión</option>
                        <option value="resuelta" {{ request('estado') == 'resuelta' ? 'selected' : '' }}>Resuelta</option>
                        <option value="conforme" {{ request('estado') == 'conforme' ? 'selected' : '' }}>Conforme</option>
                    </select>
                </div>
                <button type="submit" 
                        class="px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded hover:bg-gray-700 transition-colors">
                    Filtrar
                </button>
                @if(request()->hasAny(['fecha', 'recinto_id', 'estado']))
                    <a href="{{ route('admin.incidencias.index') }}" 
                       class="px-4 py-2 border border-gray-300 text-gray-600 text-sm font-semibold rounded hover:bg-gray-50 transition-colors">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabla de Historial -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            @if($incidencias->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Fecha</th>
                            <th class="text-left px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Instalación</th>
                            <th class="text-left px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Tipo de Incidencia</th>
                            <th class="text-left px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Responsable</th>
                            <th class="text-left px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Estado</th>
                            <th class="text-right px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($incidencias as $incidencia)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $incidencia->created_at->locale('es')->isoFormat('D MMM, YYYY') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $incidencia->recinto->nombre ?? ($incidencia->reserva->recinto->nombre ?? 'N/A') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($incidencia->tipo === 'problema_posuso')
                                    Problema Post-Uso
                                @elseif($incidencia->tipo === 'dano')
                                    Daño en Instalaciones
                                @elseif($incidencia->tipo === 'informe')
                                    <span class="text-emerald-600 font-medium">Informe</span>
                                @else
                                    Otro
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $incidencia->reportado_por ?? ($incidencia->reserva->nombre_organizacion ?? 'N/A') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($incidencia->estado === 'reportada')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Pendiente
                                    </span>
                                @elseif($incidencia->estado === 'en_revision')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        En Revisión
                                    </span>
                                @elseif($incidencia->estado === 'resuelta')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        Resuelto
                                    </span>
                                @elseif($incidencia->estado === 'conforme')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        Conforme
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        {{ ucfirst($incidencia->estado) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.incidencias.show', $incidencia) }}" 
                                   class="text-sm font-semibold text-orange-500 hover:text-orange-700 uppercase tracking-wide transition-colors">
                                    Detalles
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-400">
                        Mostrando <span class="font-bold text-gray-700">{{ $incidencias->firstItem() ?? 0 }}</span> a <span class="font-bold text-gray-700">{{ $incidencias->lastItem() ?? 0 }}</span> 
                        de <span class="font-bold text-gray-700">{{ $incidencias->total() }}</span> reportes
                    </div>
                    
                    @if($incidencias->hasPages())
                        <div class="flex items-center gap-4">
                            @if($incidencias->onFirstPage())
                                <span class="text-sm text-gray-300 cursor-not-allowed">Anterior</span>
                            @else
                                <a href="{{ $incidencias->previousPageUrl() }}" class="text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">Anterior</a>
                            @endif
                            
                            <span class="text-sm font-bold text-gray-700">Página {{ $incidencias->currentPage() }} de {{ $incidencias->lastPage() }}</span>
                            
                            @if($incidencias->hasMorePages())
                                <a href="{{ $incidencias->nextPageUrl() }}" class="text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">Siguiente</a>
                            @else
                                <span class="text-sm text-gray-300 cursor-not-allowed">Siguiente</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            @else
            <div class="px-6 py-16 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-500 font-medium">No se encontraron reportes</p>
                <p class="text-sm text-gray-400 mt-1">Los reportes de incidencias aparecerán aquí una vez creados.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    /**
     * Toggle de incidencia (No / Sí) en las tarjetas de instalaciones
     */
    function toggleIncidencia(button, value) {
        const recintoId = button.dataset.recinto;
        const card = document.querySelector(`[data-recinto-card="${recintoId}"]`);
        const toggles = card.querySelectorAll('.incidencia-toggle');
        const btnEnviar = document.querySelector(`.btn-enviar-wrapper[data-recinto="${recintoId}"]`);
        const btnReportar = document.querySelector(`.btn-reportar-wrapper[data-recinto="${recintoId}"]`);

        // Reset all toggles in this card
        toggles.forEach(t => {
            t.classList.remove('bg-orange-500', 'text-white', 'bg-gray-100', 'text-gray-600');
            t.classList.add('bg-white', 'text-gray-400');
        });

        // Activate the selected button
        button.classList.remove('bg-white', 'text-gray-400');
        if (value === 'si') {
            button.classList.add('bg-orange-500', 'text-white');
            btnEnviar.classList.add('hidden');
            btnReportar.classList.remove('hidden');
        } else {
            button.classList.add('bg-gray-100', 'text-gray-600');
            btnEnviar.classList.remove('hidden');
            btnReportar.classList.add('hidden');
        }

        // Also style the other toggle
        const otherToggle = card.querySelector(`.incidencia-toggle[data-value="${value === 'si' ? 'no' : 'si'}"]`);
        if (otherToggle) {
            otherToggle.classList.remove('bg-orange-500', 'text-white', 'bg-gray-100', 'text-gray-600');
            if (value === 'si') {
                otherToggle.classList.add('bg-white', 'text-gray-400');
            } else {
                otherToggle.classList.add('bg-white', 'text-gray-400');
            }
        }
    }

    /**
     * Enviar reporte sin incidencia (POST al servidor)
     */
    function enviarSinIncidencia(recintoId) {
        const card = document.querySelector(`[data-recinto-card="${recintoId}"]`);
        const btn = card.querySelector('.btn-enviar-wrapper button');
        
        // Deshabilitar botón mientras se envía
        btn.textContent = 'ENVIANDO...';
        btn.classList.remove('bg-gray-800', 'hover:bg-gray-700');
        btn.classList.add('bg-gray-500', 'cursor-not-allowed');
        btn.disabled = true;

        fetch('{{ route("admin.incidencias.sin-incidencia") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ recinto_id: recintoId }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                btn.textContent = '✓ REGISTRADO';
                btn.classList.remove('bg-gray-500');
                btn.classList.add('bg-green-600');
                
                // Recargar la página después de 1.5s para actualizar el historial
                setTimeout(() => { window.location.reload(); }, 1500);
            } else {
                btn.textContent = '✗ ERROR';
                btn.classList.remove('bg-gray-500');
                btn.classList.add('bg-red-600');
                setTimeout(() => {
                    btn.textContent = 'ENVIAR';
                    btn.classList.remove('bg-red-600', 'cursor-not-allowed');
                    btn.classList.add('bg-gray-800', 'hover:bg-gray-700');
                    btn.disabled = false;
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btn.textContent = '✗ ERROR';
            btn.classList.remove('bg-gray-500');
            btn.classList.add('bg-red-600');
            setTimeout(() => {
                btn.textContent = 'ENVIAR';
                btn.classList.remove('bg-red-600', 'cursor-not-allowed');
                btn.classList.add('bg-gray-800', 'hover:bg-gray-700');
                btn.disabled = false;
            }, 3000);
        });
    }
</script>
@endsection
