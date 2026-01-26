<?php

namespace App\Http\Controllers;

use App\Models\Recinto;
use App\Models\Reserva;
use App\Models\User;
use App\Services\DisponibilidadService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Controlador del Calendario de Reservas
 *
 * Maneja la visualizacion del calendario donde los usuarios pueden ver
 * que dias y horarios estan disponibles para reservar un recinto deportivo.
 */
class CalendarioController extends Controller
{
    public function __construct(
        private DisponibilidadService $disponibilidadService
    ) {}

    /**
     * Muestra el calendario de disponibilidad de recintos
     *
     * Esta pagina permite a los ciudadanos ver todos los recintos activos
     * y las reservas existentes de los proximos 30 dias.
     * Ahora puede filtrar por un recinto especifico si se pasa recinto_id
     */
    public function index(Request $request)
    {
        // Obtiene solo los recintos que estan activos y disponibles para reservar
        $recintos = Recinto::activos()->get();

        // Verifica si se esta filtrando por un recinto especifico
        $recintoSeleccionado = null;
        $encargadoInfo = null;

        if ($request->has('recinto')) {
            $recintoId = $request->get('recinto');
            $recintoSeleccionado = Recinto::activos()->find($recintoId);

            // Si existe el recinto, obtener información del encargado desde la base de datos
            if ($recintoSeleccionado) {
                // Buscar encargado asignado a este recinto
                $encargado = User::where('recinto_asignado_id', $recintoId)
                                 ->where('role', 'encargado_recinto')
                                 ->where('activo', 1)
                                 ->first();

                // Si se encuentra un encargado, pasar su información
                if ($encargado) {
                    $encargadoInfo = [
                        'nombre' => $encargado->name,
                        'email' => $encargado->email,
                    ];
                }
            }
        }

        // Define el rango de fechas a mostrar (desde hoy hasta 30 dias despues)
        $fechaInicio = Carbon::now()->startOfDay();
        $fechaFin = Carbon::now()->addDays(30)->endOfDay();

        // Obtiene todas las reservas aprobadas y vigentes para mostrar en el calendario
        // Las agrupa por recinto y por fecha para facilitar la visualizacion
        $reservas = Reserva::with('recinto')
            ->aprobadas()
            ->whereNull('fecha_cancelacion')
            ->whereBetween('fecha_reserva', [$fechaInicio, $fechaFin])
            ->get()
            ->groupBy(['recinto_id', 'fecha_reserva']);

        return view('calendario.index', compact('recintos', 'reservas', 'fechaInicio', 'fechaFin', 'recintoSeleccionado', 'encargadoInfo'));
    }

    /**
     * Consulta la disponibilidad horaria de un recinto para una fecha especifica
     *
     * Esta funcion es llamada por JavaScript cuando el usuario selecciona
     * una fecha en el calendario. Retorna todas las franjas horarias del dia
     * indicando cuales estan libres, ocupadas o bloqueadas.
     */
    public function disponibilidad(Request $request)
    {
        $recintoId = $request->get('recinto_id');
        $fecha = $request->get('fecha');

        // Valida que se hayan enviado los parametros requeridos
        if (!$recintoId || !$fecha) {
            return response()->json(['error' => 'Parámetros inválidos'], 400);
        }

        // Busca el recinto en la base de datos
        $recinto = Recinto::find($recintoId);
        if (!$recinto) {
            return response()->json(['error' => 'Recinto no encontrado'], 404);
        }

        // Valida el formato de fecha
        try {
            Carbon::parse($fecha);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Fecha inválida'], 400);
        }

        // Usa el servicio para calcular la disponibilidad
        $resultado = $this->disponibilidadService->calcularDisponibilidad($recinto, $fecha);

        if (isset($resultado['error'])) {
            return response()->json(['error' => $resultado['error']], 400);
        }

        return response()->json($resultado);
    }

    /**
     * Calcula el estado general de un dia para un recinto
     * Retorna: 'DISPONIBLE', 'OCUPADO', o 'MANTENIMIENTO'
     */
    public function estadoDia(Request $request)
    {
        $recintoId = $request->get('recinto_id');
        $fecha = $request->get('fecha');

        if (!$recintoId || !$fecha) {
            return response()->json(['error' => 'Parámetros inválidos'], 400);
        }

        $recinto = Recinto::find($recintoId);
        if (!$recinto) {
            return response()->json(['error' => 'Recinto no encontrado'], 404);
        }

        try {
            Carbon::parse($fecha);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Fecha inválida'], 400);
        }

        $estado = $this->disponibilidadService->calcularEstadoDia($recinto, $fecha);

        return response()->json(['estado' => $estado]);
    }

    /**
     * Obtiene los estados de todos los días de un mes
     * Retorna un array con fecha => estado para optimizar carga del calendario
     */
    public function estadosMes(Request $request)
    {
        $recintoId = $request->get('recinto_id');
        $año = $request->get('año');
        $mes = $request->get('mes'); // 1-12

        if (!$recintoId || !$año || !$mes) {
            return response()->json(['error' => 'Parámetros inválidos'], 400);
        }

        $recinto = Recinto::find($recintoId);
        if (!$recinto) {
            return response()->json(['error' => 'Recinto no encontrado'], 404);
        }

        $estados = $this->disponibilidadService->calcularEstadosMes($recinto, (int) $año, (int) $mes);

        return response()->json(['estados' => $estados]);
    }
}
