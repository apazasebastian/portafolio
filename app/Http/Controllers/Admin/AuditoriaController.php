<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    /**
     * Mostrar listado de logs de auditoría
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

        // Filtro por búsqueda en descripción
        if ($request->filled('buscar')) {
            $query->where('description', 'like', '%' . $request->buscar . '%');
        }

        $logs = $query->paginate(50);

        // Obtener usuarios para el filtro
        $usuarios = User::orderBy('name')->get();

        // Obtener acciones únicas para el filtro
        $acciones = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // Estadísticas rápidas
        $estadisticas = [
            'total_logs' => AuditLog::count(),
            'hoy' => AuditLog::whereDate('created_at', today())->count(),
            'esta_semana' => AuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'este_mes' => AuditLog::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.auditoria.index', compact('logs', 'usuarios', 'acciones', 'estadisticas'));
    }

    /**
     * Mostrar detalles de un log
     */
    public function show(AuditLog $log)
    {
        $log->load('user');
        
        return view('admin.auditoria.show', compact('log'));
    }
}