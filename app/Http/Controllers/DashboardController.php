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
                'pendiente' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-400'],
                'aprobada' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-400'],
                'rechazada' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-400']
            ];

            // Generar HTML de la tabla
            $html = '';
            
            if ($reservas->count() > 0) {
                foreach ($reservas as $reserva) {
                    $config = $estadoConfig[$reserva->estado] ?? $estadoConfig['pendiente'];
                    $iniciales = strtoupper(substr($reserva->nombre_organizacion ?? $reserva->representante_nombre, 0, 2));
                    
                    $html .= '<tr class="hover:bg-gray-50 transition-colors">';
                    $html .= '<td class="px-6 py-4 whitespace-nowrap"><span class="text-sm font-mono font-bold text-gray-900">#' . $reserva->id . '</span></td>';
                    $html .= '<td class="px-6 py-4"><div class="flex items-center">';
                    $html .= '<div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-gray-500 to-gray-700 rounded-full flex items-center justify-center">';
                    $html .= '<span class="text-white font-bold text-sm">' . $iniciales . '</span></div>';
                    $html .= '<div class="ml-3"><div class="text-sm font-medium text-gray-900">' . e($reserva->nombre_organizacion ?? 'Sin organización') . '</div>';
                    $html .= '<div class="text-xs text-gray-500">' . e($reserva->representante_nombre) . '</div></div></div></td>';
                    $html .= '<td class="px-6 py-4"><div class="text-sm font-medium text-gray-900">' . e($reserva->recinto->nombre ?? 'N/A') . '</div></td>';
                    $html .= '<td class="px-6 py-4"><div class="text-sm"><div class="font-medium text-gray-900">' . $reserva->fecha_reserva->format('d/m/Y') . '</div>';
                    $html .= '<div class="text-xs text-gray-600">' . \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') . ' - ' . \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') . '</div></div></td>';
                    $html .= '<td class="px-6 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-300">' . e($reserva->deporte) . '</span></td>';
                    $html .= '<td class="px-6 py-4 text-sm text-gray-900 text-center"><span class="font-semibold">' . $reserva->cantidad_personas . '</span></td>';
                    $html .= '<td class="px-6 py-4 whitespace-nowrap"><div class="space-y-1">';
                    $html .= '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border ' . $config['bg'] . ' ' . $config['text'] . ' ' . $config['border'] . '">' . ucfirst($reserva->estado) . '</span>';
                    if ($reserva->fecha_cancelacion) {
                        $html .= '<div><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-400">Cancelada</span></div>';
                    }
                    $html .= '</div></td>';
                    $html .= '<td class="px-6 py-4 text-center"><div class="flex items-center justify-center gap-2 flex-wrap">';
                    $html .= '<a href="' . route('admin.reservas.show', $reserva) . '" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors" title="Ver detalles">';
                    $html .= '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Ver</a>';
                    
                    if ($reserva->puedeReportarIncidencia() && !$reserva->fecha_cancelacion) {
                        $html .= '<a href="' . route('admin.incidencias.crear', $reserva->id) . '" class="inline-flex items-center px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-md transition-colors" title="Reportar incidencia">';
                        $html .= '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>Incidencia';
                        if ($reserva->tieneIncidencias()) {
                            $html .= '<span class="ml-1 bg-white text-orange-600 rounded-full px-1.5 text-xs font-bold">' . $reserva->cantidadIncidencias() . '</span>';
                        }
                        $html .= '</a>';
                    }
                    
                    $html .= '</div></td></tr>';
                }
            } else {
                $html = '<tr><td colspan="8" class="px-6 py-12 text-center">';
                $html .= '<svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
                $html .= '<p class="text-lg font-medium text-gray-500 mt-4">No hay reservas que coincidan con los filtros</p>';
                $html .= '<p class="text-sm text-gray-400 mt-1">Intenta cambiar los criterios de búsqueda</p>';
                $html .= '</td></tr>';
            }

            return response()->json([
                'html' => $html,
                'paginacion' => $reservas->links()->toHtml(),
                'total' => $reservas->total(),
                'desde' => $reservas->firstItem() ?? 0,
                'hasta' => $reservas->lastItem() ?? 0
            ]);
        }

        return view('admin.dashboard', compact(
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