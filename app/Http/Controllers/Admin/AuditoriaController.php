<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuditoriaController extends Controller
{
    /**
     * Mostrar listado de logs de auditoría con filtros
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Filtro por usuario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por acción
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filtro por fecha desde
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        // Filtro por fecha hasta
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // Búsqueda en descripción
        if ($request->filled('buscar')) {
            $query->where('description', 'like', '%' . $request->buscar . '%');
        }

        // Paginación
        $logs = $query->paginate(50)->withQueryString();

        // Estadísticas
        $estadisticas = [
            'total' => AuditLog::count(),
            'hoy' => AuditLog::whereDate('created_at', today())->count(),
            'esta_semana' => AuditLog::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'este_mes' => AuditLog::whereMonth('created_at', now()->month)->count(),
        ];

        // Datos para filtros
        $usuarios = User::orderBy('name')->get();
        $acciones = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('admin.auditoria.index', compact('logs', 'usuarios', 'acciones', 'estadisticas'));
    }

    /**
     * Mostrar detalles de un log específico
     */
    public function show(AuditLog $log)
    {
        $log->load('user');
        return view('admin.auditoria.show', compact('log'));
    }

    /**
     *  ACTUALIZADO: Exportar logs de auditoría a Excel (CSV) + REGISTRAR EN AUDITORÍA
     */
    public function exportar(Request $request)
    {
        try {
            $query = AuditLog::with('user')->orderBy('created_at', 'desc');

            // Aplicar los mismos filtros que en index()
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }
            if ($request->filled('fecha_desde')) {
                $query->whereDate('created_at', '>=', $request->fecha_desde);
            }
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('created_at', '<=', $request->fecha_hasta);
            }
            if ($request->filled('buscar')) {
                $query->where('description', 'like', '%' . $request->buscar . '%');
            }

            $logs = $query->get();
            $totalRegistros = $logs->count();

            $csvFileName = 'auditoria_' . Carbon::now()->format('d-m-Y-His') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
            ];

            //  REGISTRAR EXPORTACIÓN EN AUDITORÍA 
            $filtrosAplicados = [];
            if ($request->filled('user_id')) {
                $usuario = User::find($request->user_id);
                $filtrosAplicados[] = "Usuario: {$usuario->name}";
            }
            if ($request->filled('action')) {
                $filtrosAplicados[] = "Acción: {$request->action}";
            }
            if ($request->filled('fecha_desde')) {
                $filtrosAplicados[] = "Desde: {$request->fecha_desde}";
            }
            if ($request->filled('fecha_hasta')) {
                $filtrosAplicados[] = "Hasta: {$request->fecha_hasta}";
            }
            if ($request->filled('buscar')) {
                $filtrosAplicados[] = "Búsqueda: {$request->buscar}";
            }

            $descripcionFiltros = empty($filtrosAplicados) 
                ? 'sin filtros' 
                : 'con filtros: ' . implode(', ', $filtrosAplicados);

            AuditLog::log(
                'exportar_excel',
                "Exportación de auditoría a Excel ({$totalRegistros} registros) {$descripcionFiltros}",
                null,
                null,
                null
            );

            $callback = function () use ($logs) {
                $file = fopen('php://output', 'w');
                
                // BOM para UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Encabezados
                fputcsv($file, [
                    'ID',
                    'Fecha y Hora',
                    'Usuario',
                    'Email',
                    'Rol',
                    'Acción',
                    'Descripción',
                    'IP',
                    'Navegador'
                ], ',');

                // Datos
                foreach ($logs as $log) {
                    fputcsv($file, [
                        $log->id,
                        $log->created_at->format('d/m/Y H:i:s'),
                        $log->user_name,
                        $log->user_email,
                        $log->user_role,
                        $log->action,
                        $log->description,
                        $log->ip_address,
                        $log->user_agent
                    ], ',');
                }
                
                fclose($file);
            };

            return response()->streamDownload($callback, $csvFileName, $headers);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al exportar auditoría: ' . $e->getMessage());
        }
    }
}