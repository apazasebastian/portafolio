<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Recinto;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Estadísticas generales
        $reservasPendientes = Reserva::where('estado', 'pendiente')->count();
        $reservasHoy = Reserva::whereDate('fecha_reserva', today())->count();
        $reservasEstesMes = Reserva::whereMonth('fecha_reserva', now()->month)
            ->whereYear('fecha_reserva', now()->year)
            ->count();
        $recintosActivos = Recinto::where('activo', true)->count();

        // Contadores por estado
        $todasReservas = Reserva::count();
        $contadorAprobadas = Reserva::where('estado', 'aprobada')->count();
        $contadorRechazadas = Reserva::where('estado', 'rechazada')->count();
        $contadorCanceladas = Reserva::whereNotNull('fecha_cancelacion')->count();

        // Query base para reservas
        $query = Reserva::with(['recinto']);

        // Filtro por estado
        $estado = $request->get('estado', '');
        
        if ($estado === 'pendiente') {
            $query->where('estado', 'pendiente');
        } elseif ($estado === 'aprobada') {
            $query->where('estado', 'aprobada');
        } elseif ($estado === 'rechazada') {
            $query->where('estado', 'rechazada');
        } elseif ($estado === 'cancelada') {
            $query->whereNotNull('fecha_cancelacion');
        }

        // Filtros avanzados
        if ($request->filled('recinto_id')) {
            $query->where('recinto_id', $request->recinto_id);
        }

        if ($request->filled('deporte')) {
            $query->where('deporte', $request->deporte);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_reserva', $request->fecha);
        }

        // Búsqueda por RUT
        if ($request->filled('buscar_rut')) {
            $rutBuscar = str_replace(['.', '-', ' '], '', $request->buscar_rut);
            $query->whereRaw("REPLACE(REPLACE(REPLACE(rut, '.', ''), '-', ''), ' ', '') LIKE ?", ['%' . $rutBuscar . '%']);
        }
        
        // Búsqueda por Organización
        if ($request->filled('buscar_organizacion')) {
            $query->where('nombre_organizacion', 'LIKE', '%' . $request->buscar_organizacion . '%');
        }

        // Ordenar y paginar
        $reservas = $query->orderBy('fecha_reserva', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Obtener recintos para el filtro
        $recintos = Recinto::all();

        // DATOS PARA EL CALENDARIO INTERACTIVO
        $todasReservasCalendario = Reserva::with('recinto')
            ->select('id', 'recinto_id', 'nombre_organizacion', 'fecha_reserva', 'hora_inicio', 'hora_fin', 'estado')
            ->whereYear('fecha_reserva', now()->year)
            ->whereIn('estado', ['pendiente', 'aprobada'])  // ✅ AGREGADO
            ->get()
            ->map(function($reserva) {
                return [
                    'id' => $reserva->id,
                    'organizacion' => $reserva->nombre_organizacion,
                    'recinto' => $reserva->recinto->nombre ?? 'N/A',
                    'fecha' => $reserva->fecha_reserva->format('Y-m-d'),
                    'hora_inicio' => \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i'),
                    'hora_fin' => \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i'),
                    'estado' => $reserva->estado
                ];
            });

        // Si es petición AJAX, devolver JSON con HTML de la tabla
        if ($request->ajax() || $request->get('ajax')) {
            $estadoConfig = [
                'pendiente' => ['text' => 'text-yellow-600'],
                'aprobada' => ['text' => 'text-green-600'],
                'rechazada' => ['text' => 'text-red-600']
            ];

            // Generar HTML de la tabla (estilo minimalista)
            $html = '';
            
            if ($reservas->count() > 0) {
                foreach ($reservas as $reserva) {
                    $config = $estadoConfig[$reserva->estado] ?? $estadoConfig['pendiente'];
                    $iniciales = strtoupper(substr($reserva->nombre_organizacion ?? $reserva->representante_nombre, 0, 2));
                    
                    $html .= '<tr class="hover:bg-gray-50/50 transition-colors">';
                    $html .= '<td class="px-6 py-5 whitespace-nowrap"><span class="text-sm text-gray-400 font-medium">#' . $reserva->id . '</span></td>';
                    $html .= '<td class="px-6 py-5"><div class="flex items-center">';
                    $html .= '<div class="flex-shrink-0 h-10 w-10 bg-gray-800 rounded-full flex items-center justify-center">';
                    $html .= '<span class="text-white font-bold text-sm">' . $iniciales . '</span></div>';
                    $html .= '<div class="ml-4"><div class="text-sm font-bold text-gray-900">' . e($reserva->nombre_organizacion ?? 'Sin organización') . '</div>';
                    $html .= '<div class="text-xs text-gray-400">' . e($reserva->representante_nombre) . '</div></div></div></td>';
                    $html .= '<td class="px-6 py-5"><div class="text-sm text-gray-700">' . e($reserva->recinto->nombre ?? 'N/A') . '</div></td>';
                    $html .= '<td class="px-6 py-5"><div class="text-sm"><div class="font-bold text-gray-900">' . $reserva->fecha_reserva->format('d/m/Y') . '</div>';
                    $html .= '<div class="text-xs text-gray-400">' . \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') . ' - ' . \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') . '</div></div></td>';
                    $html .= '<td class="px-6 py-5"><span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white text-gray-600 border border-gray-200">' . e($reserva->deporte) . '</span></td>';
                    $html .= '<td class="px-6 py-5 text-center"><span class="text-sm font-bold text-gray-900">' . $reserva->cantidad_personas . '</span></td>';
                    $html .= '<td class="px-6 py-5 whitespace-nowrap"><div class="space-y-1">';
                    $html .= '<span class="text-xs font-bold uppercase tracking-wide ' . $config['text'] . '">' . strtoupper($reserva->estado) . '</span>';
                    if ($reserva->fecha_cancelacion) {
                        $html .= '<div><span class="text-xs text-gray-400 italic">Cancelada</span></div>';
                    }
                    $html .= '</div></td>';
                    $html .= '<td class="px-6 py-5 text-right"><div class="flex items-center justify-end gap-4">';
                    $html .= '<a href="' . route('admin.reservas.show', $reserva) . '" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors" title="Ver detalles">Ver</a>';
                    
                    if ($reserva->puedeReportarIncidencia() && !$reserva->fecha_cancelacion) {
                        $html .= '<a href="' . route('admin.incidencias.crear', $reserva->id) . '" class="text-sm font-medium text-orange-500 hover:text-orange-700 transition-colors" title="Reportar incidencia">Incidencia';
                        if ($reserva->tieneIncidencias()) {
                            $html .= '<span class="ml-1 text-orange-600 font-bold">' . $reserva->cantidadIncidencias() . '</span>';
                        }
                        $html .= '</a>';
                    }
                    
                    $html .= '</div></td></tr>';
                }
            } else {
                $html = '<tr><td colspan="8" class="px-6 py-16 text-center">';
                $html .= '<svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
                $html .= '<p class="text-base font-medium text-gray-400 mt-4">No hay reservas que coincidan con los filtros</p>';
                $html .= '<p class="text-sm text-gray-300 mt-1">Intenta cambiar los criterios de búsqueda</p>';
                $html .= '</td></tr>';
            }

            return response()->json([
                'html' => $html,
                'pagination' => [
                    'current_page' => $reservas->currentPage(),
                    'last_page' => $reservas->lastPage(),
                    'has_more_pages' => $reservas->hasMorePages(),
                    'on_first_page' => $reservas->onFirstPage(),
                ],
                'total' => $reservas->total(),
                'desde' => $reservas->firstItem() ?? 0,
                'hasta' => $reservas->lastItem() ?? 0
            ]);
        }

        $response = response()->view('admin.dashboard', compact(
            'reservasPendientes',
            'reservasHoy',
            'reservasEstesMes',
            'recintosActivos',
            'todasReservas',
            'contadorAprobadas',
            'contadorRechazadas',
            'contadorCanceladas',
            'reservas',
            'recintos',
            'todasReservasCalendario'
        ));

        // Prevenir caché para evitar problemas al navegar con botón atrás
        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', '0');

        return $response;
    }

    /**
     * Exportar reservas del dashboard a Excel (CSV)
     */
    public function exportar(Request $request)
    {
        try {
            // Construir query con los mismos filtros del dashboard
            $query = Reserva::with(['recinto']);

            $estado = $request->get('estado', '');
            
            if ($estado === 'pendiente') {
                $query->where('estado', 'pendiente');
            } elseif ($estado === 'aprobada') {
                $query->where('estado', 'aprobada');
            } elseif ($estado === 'rechazada') {
                $query->where('estado', 'rechazada');
            } elseif ($estado === 'cancelada') {
                $query->whereNotNull('fecha_cancelacion');
            }

            if ($request->filled('recinto_id')) {
                $query->where('recinto_id', $request->recinto_id);
            }

            if ($request->filled('deporte')) {
                $query->where('deporte', $request->deporte);
            }

            if ($request->filled('fecha')) {
                $query->whereDate('fecha_reserva', $request->fecha);
            }

            if ($request->filled('buscar_rut')) {
                $rutBuscar = str_replace(['.', '-', ' '], '', $request->buscar_rut);
                $query->whereRaw("REPLACE(REPLACE(REPLACE(rut, '.', ''), '-', ''), ' ', '') LIKE ?", ['%' . $rutBuscar . '%']);
            }
            
            if ($request->filled('buscar_organizacion')) {
                $query->where('nombre_organizacion', 'LIKE', '%' . $request->buscar_organizacion . '%');
            }

            $reservas = $query->orderBy('fecha_reserva', 'desc')->get();

            $csvFileName = 'reservas_' . Carbon::now()->format('d-m-Y-His') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
            ];

            $callback = function () use ($reservas) {
                $file = fopen('php://output', 'w');
                
                // BOM para UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Encabezados
                fputcsv($file, [
                    'ID',
                    'Organización',
                    'Representante',
                    'Email',
                    'Teléfono',
                    'RUT',
                    'Recinto',
                    'Deporte',
                    'Fecha',
                    'Hora Inicio',
                    'Hora Fin',
                    'Personas',
                    'Estado',
                    'Fecha Aprobación/Rechazo',
                    'Motivo Rechazo',
                    'Cancelada'
                ], ',');

                // Datos
                foreach ($reservas as $reserva) {
                    fputcsv($file, [
                        $reserva->id,
                        $reserva->nombre_organizacion ?? 'N/A',
                        $reserva->representante_nombre,
                        $reserva->email,
                        $reserva->telefono,
                        $reserva->rut,
                        $reserva->recinto->nombre ?? 'N/A',
                        $reserva->deporte,
                        $reserva->fecha_reserva->format('d/m/Y'),
                        \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i'),
                        \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i'),
                        $reserva->cantidad_personas,
                        ucfirst($reserva->estado),
                        $reserva->fecha_respuesta ? $reserva->fecha_respuesta->format('d/m/Y H:i') : 'N/A',
                        $reserva->motivo_rechazo ?? 'N/A',
                        $reserva->fecha_cancelacion ? 'Sí' : 'No'
                    ], ',');
                }
                
                fclose($file);
            };

            return response()->streamDownload($callback, $csvFileName, $headers);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al exportar reservas: ' . $e->getMessage());
        }
    }
}