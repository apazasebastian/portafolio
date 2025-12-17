@extends('layouts.app')

@section('title', 'Reporte Histórico por Organización')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Reporte Histórico por Organización</h1>
                <p class="text-gray-600 mt-2">Análisis detallado del uso de recintos deportivos</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.estadisticas.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>
                <button id="btnExportar" onclick="exportarReporte()" disabled
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exportar PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Buscador de Organización -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Buscar Organización
        </label>
        <div class="flex gap-4">
            <div class="flex-1 relative">
                <input 
                    type="text" 
                    id="buscador"
                    placeholder="Ingrese el nombre de la organización o RUT..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <div id="sugerencias" class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden"></div>
            </div>
            <button onclick="buscarOrganizacion()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition">
                Generar Reporte
            </button>
        </div>
    </div>

    <!-- Loading -->
    <div id="loading" class="hidden bg-white rounded-lg shadow-md p-12 text-center">
        <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600">Generando reporte...</p>
    </div>

    <!-- Contenedor de resultados -->
    <div id="resultados" class="hidden">
        <!-- Información de la Organización -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-md p-6 mb-6 text-white">
            <div class="flex items-center gap-4">
                <div class="bg-white/20 rounded-full p-4">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 id="nombreOrg" class="text-2xl font-bold"></h2>
                    <p id="infoOrg" class="text-blue-100 mt-1"></p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold" id="totalReservas">0</div>
                    <div class="text-blue-100 text-sm">Reservas Totales</div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Generales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Aprobadas</p>
                        <p id="statAprobadas" class="text-3xl font-bold text-green-600 mt-1">0</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <span id="pctAprobadas" class="text-sm text-gray-600">0%</span>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Pendientes</p>
                        <p id="statPendientes" class="text-3xl font-bold text-yellow-600 mt-1">0</p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <span id="pctPendientes" class="text-sm text-gray-600">0%</span>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Rechazadas</p>
                        <p id="statRechazadas" class="text-3xl font-bold text-red-600 mt-1">0</p>
                    </div>
                    <div class="bg-red-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <span id="pctRechazadas" class="text-sm text-gray-600">0%</span>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Horas Totales</p>
                        <p id="statHoras" class="text-3xl font-bold text-blue-600 mt-1">0</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <span id="horasPromedio" class="text-sm text-gray-600">0h por reserva</span>
                </div>
            </div>
        </div>

        <!-- Análisis de Patrones -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4"> Análisis de Patrones de Uso</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="border-l-4 border-blue-500 pl-4">
                    <p class="text-sm text-gray-600">Día Favorito</p>
                    <p id="diaFavorito" class="text-xl font-bold text-gray-900 mt-1">-</p>
                </div>
                <div class="border-l-4 border-purple-500 pl-4">
                    <p class="text-sm text-gray-600">Horario Preferido</p>
                    <p id="horarioFavorito" class="text-xl font-bold text-gray-900 mt-1">-</p>
                </div>
                <div class="border-l-4 border-green-500 pl-4">
                    <p class="text-sm text-gray-600">Tasa de Aprobación</p>
                    <p id="tasaAprobacion" class="text-xl font-bold text-gray-900 mt-1">0%</p>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribución por Estado</h3>
                <canvas id="chartEstados"></canvas>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Deportes Más Solicitados</h3>
                <canvas id="chartDeportes"></canvas>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Reservas en el Tiempo (Últimos 12 meses)</h3>
                <canvas id="chartTemporal"></canvas>
            </div>
        </div>

        <!-- Top Recintos -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">🏟️ Recintos Más Utilizados</h3>
            <div id="topRecintos" class="space-y-3"></div>
        </div>

        <!-- Tabla de Reservas Detallada -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800"> Historial Completo de Reservas</h3>
                    <select id="filtroEstado" onchange="filtrarTabla()" class="rounded-md border-gray-300 text-sm">
                        <option value="">Todos los estados</option>
                        <option value="aprobada">Aprobadas</option>
                        <option value="pendiente">Pendientes</option>
                        <option value="rechazada">Rechazadas</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Horario</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Recinto</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Deporte</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Personas</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaReservas" class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <p id="contadorReservas" class="text-sm text-gray-600"></p>
            </div>
        </div>
    </div>

    <!-- Estado vacío -->
    <div id="estadoVacio" class="bg-white rounded-lg shadow-md p-12 text-center">
        <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Busca una organización</h3>
        <p class="text-gray-500">Ingresa el nombre de una organización para ver su reporte histórico completo</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let orgSeleccionada = null;
    let datosReporte = null;
    let reservasFiltradas = [];
    let charts = {};

    // Autocompletado
    document.getElementById('buscador').addEventListener('input', async function(e) {
        const valor = e.target.value;
        const sugerencias = document.getElementById('sugerencias');
        
        if (valor.length < 2) {
            sugerencias.classList.add('hidden');
            return;
        }

        try {
            const response = await fetch(`{{ route('admin.reportes.buscar') }}?termino=${encodeURIComponent(valor)}`);
            const organizaciones = await response.json();

            if (organizaciones.length > 0) {
                sugerencias.innerHTML = organizaciones.map(org => `
                    <div class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100" 
                         onclick='seleccionarOrganizacion(${JSON.stringify(org).replace(/'/g, "&apos;")})'>
                        <div class="font-medium text-gray-900">${org.nombre_organizacion}</div>
                        <div class="text-sm text-gray-500">${org.representante_nombre} • ${org.email}</div>
                    </div>
                `).join('');
                sugerencias.classList.remove('hidden');
            } else {
                sugerencias.classList.add('hidden');
            }
        } catch (error) {
            console.error('Error al buscar organizaciones:', error);
        }
    });

    function seleccionarOrganizacion(org) {
        orgSeleccionada = org;
        document.getElementById('buscador').value = org.nombre_organizacion;
        document.getElementById('sugerencias').classList.add('hidden');
    }

    async function buscarOrganizacion() {
        const nombreOrg = document.getElementById('buscador').value.trim();
        
        if (!nombreOrg) {
            alert('Por favor ingrese un nombre de organización');
            return;
        }

        document.getElementById('estadoVacio').classList.add('hidden');
        document.getElementById('resultados').classList.add('hidden');
        document.getElementById('loading').classList.remove('hidden');

        try {
            const response = await fetch('{{ route('admin.reportes.generar') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ organizacion: nombreOrg })
            });

            const data = await response.json();

            if (data.success) {
                datosReporte = data;
                mostrarReporte(data);
                document.getElementById('btnExportar').disabled = false;
            } else {
                alert(data.message || 'No se encontraron datos');
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('estadoVacio').classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al generar el reporte');
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('estadoVacio').classList.remove('hidden');
        }
    }

    function mostrarReporte(data) {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('resultados').classList.remove('hidden');

        document.getElementById('nombreOrg').textContent = data.organizacion.nombre;
        document.getElementById('infoOrg').textContent = `${data.organizacion.representante} • ${data.organizacion.email}`;
        document.getElementById('totalReservas').textContent = data.estadisticas.total;

        document.getElementById('statAprobadas').textContent = data.estadisticas.aprobadas;
        document.getElementById('statPendientes').textContent = data.estadisticas.pendientes;
        document.getElementById('statRechazadas').textContent = data.estadisticas.rechazadas;
        document.getElementById('statHoras').textContent = data.estadisticas.horas_totales;
        
        document.getElementById('pctAprobadas').textContent = `${data.estadisticas.pct_aprobadas}% del total`;
        document.getElementById('pctPendientes').textContent = `${data.estadisticas.pct_pendientes}% del total`;
        document.getElementById('pctRechazadas').textContent = `${data.estadisticas.pct_rechazadas}% del total`;
        document.getElementById('horasPromedio').textContent = `${data.estadisticas.horas_promedio}h por reserva`;

        document.getElementById('diaFavorito').textContent = data.analisis.dia_semana_favorito;
        document.getElementById('horarioFavorito').textContent = data.analisis.horario_favorito;
        document.getElementById('tasaAprobacion').textContent = `${data.analisis.tasa_aprobacion}%`;

        crearGraficos(data);
        mostrarTopRecintos(data.recintos);
        reservasFiltradas = data.reservas;
        renderizarTabla(data.reservas);
    }

    function crearGraficos(data) {
        Object.values(charts).forEach(chart => chart.destroy());

        const ctxEstados = document.getElementById('chartEstados').getContext('2d');
        charts.estados = new Chart(ctxEstados, {
            type: 'doughnut',
            data: {
                labels: ['Aprobadas', 'Pendientes', 'Rechazadas'],
                datasets: [{
                    data: [data.estadisticas.aprobadas, data.estadisticas.pendientes, data.estadisticas.rechazadas],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        const ctxDeportes = document.getElementById('chartDeportes').getContext('2d');
        charts.deportes = new Chart(ctxDeportes, {
            type: 'bar',
            data: {
                labels: Object.keys(data.deportes),
                datasets: [{ label: 'Reservas', data: Object.values(data.deportes), backgroundColor: '#3b82f6' }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });

        const ctxTemporal = document.getElementById('chartTemporal').getContext('2d');
        charts.temporal = new Chart(ctxTemporal, {
            type: 'line',
            data: {
                labels: Object.keys(data.reservas_por_mes),
                datasets: [{
                    label: 'Reservas por Mes',
                    data: Object.values(data.reservas_por_mes),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });
    }

    function mostrarTopRecintos(recintos) {
        const html = Object.entries(recintos).map(([nombre, datos], index) => `
            <div class="flex items-center gap-4">
                <div class="text-2xl font-bold text-gray-400 w-8">${index + 1}</div>
                <div class="flex-1">
                    <div class="flex justify-between mb-1">
                        <span class="font-medium text-gray-700">${nombre}</span>
                        <span class="text-sm text-gray-600">${datos.cantidad} reservas (${datos.porcentaje}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: ${datos.porcentaje}%"></div>
                    </div>
                </div>
            </div>
        `).join('');

        document.getElementById('topRecintos').innerHTML = html || '<p class="text-gray-500 text-center">No hay datos disponibles</p>';
    }

    function renderizarTabla(reservas) {
        const tbody = document.getElementById('tablaReservas');
        const estadoClases = {
            'aprobada': 'bg-green-100 text-green-800',
            'pendiente': 'bg-yellow-100 text-yellow-800',
            'rechazada': 'bg-red-100 text-red-800'
        };

        tbody.innerHTML = reservas.map(r => `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-900">#${r.id}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${r.fecha_formato}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${r.hora_inicio} - ${r.hora_fin}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${r.recinto}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${r.deporte}</td>
                <td class="px-6 py-4 text-sm text-gray-900 text-center">${r.personas}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${estadoClases[r.estado]}">
                        ${r.estado.charAt(0).toUpperCase() + r.estado.slice(1)}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <a href="{{ url('admin/reservas') }}/${r.id}" class="text-blue-600 hover:text-blue-800 text-sm">
                        Ver detalles
                    </a>
                </td>
            </tr>
        `).join('');

        document.getElementById('contadorReservas').textContent = `Mostrando ${reservas.length} de ${reservasFiltradas.length} reservas`;
    }

    function filtrarTabla() {
        const filtro = document.getElementById('filtroEstado').value;
        const reservas = filtro ? reservasFiltradas.filter(r => r.estado === filtro) : reservasFiltradas;
        renderizarTabla(reservas);
    }

    function exportarReporte() {
        if (!datosReporte) {
            alert('Primero debe generar un reporte');
            return;
        }
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.reportes.exportar') }}';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        
        const orgInput = document.createElement('input');
        orgInput.type = 'hidden';
        orgInput.name = 'organizacion';
        orgInput.value = datosReporte.organizacion.nombre;
        
        form.appendChild(csrfInput);
        form.appendChild(orgInput);
        document.body.appendChild(form);
        form.submit();
    }

    // Cerrar sugerencias al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!document.getElementById('buscador').contains(e.target) && 
            !document.getElementById('sugerencias').contains(e.target)) {
            document.getElementById('sugerencias').classList.add('hidden');
        }
    });
</script>

@endsection