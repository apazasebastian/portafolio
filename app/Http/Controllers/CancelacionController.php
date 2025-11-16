<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservaCancelada;

class CancelacionController extends Controller
{
    /**
     * Mostrar el formulario de cancelación
     */
    public function mostrarFormulario()
    {
        return view('reservas.cancelar');
    }
    
    /**
     * Buscar reserva por código
     */
    public function buscarReserva(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string'
        ]);
        
        $codigo = strtoupper(trim($request->codigo));
        $reserva = Reserva::buscarPorCodigo($codigo);
        
        if (!$reserva) {
            return back()->withErrors(['codigo' => 'El código ingresado no es válido o no existe.']);
        }
        
        // Verificar si la reserva puede cancelarse
        if (!$reserva->esCancelable()) {
            $mensaje = 'Esta reserva no puede ser cancelada. ';
            
            if ($reserva->estado === 'cancelada') {
                $mensaje .= 'La reserva ya fue cancelada anteriormente.';
            } elseif ($reserva->estado === 'rechazada') {
                $mensaje .= 'La reserva fue rechazada y no requiere cancelación.';
            } elseif ($reserva->estado === 'pendiente') {
                $mensaje .= 'La reserva aún está pendiente de aprobación.';
            } else {
                $mensaje .= 'La fecha de la reserva ya pasó.';
            }
            
            return back()->withErrors(['codigo' => $mensaje]);
        }
        
        // Cargar relación del recinto
        $reserva->load('recinto');
        
        return view('reservas.confirmar-cancelacion', compact('reserva'));
    }
    
    /**
     * Procesar la cancelación
     */
    public function cancelar(Request $request, $codigo)
    {
        $request->validate([
            'motivo' => 'required|string|max:500'
        ]);
        
        $reserva = Reserva::buscarPorCodigo($codigo);
        
        if (!$reserva) {
            return redirect()->route('cancelacion.formulario')
                ->withErrors(['error' => 'Reserva no encontrada.']);
        }
        
        if (!$reserva->esCancelable()) {
            return redirect()->route('cancelacion.formulario')
                ->withErrors(['error' => 'Esta reserva no puede ser cancelada.']);
        }
        
        // Cancelar la reserva
        $reserva->cancelarPorUsuario($request->motivo);
        
        // Enviar correo de confirmación de cancelación
        try {
            Mail::to($reserva->email)->send(new ReservaCancelada($reserva));
        } catch (\Exception $e) {
            \Log::error('Error enviando correo de cancelación: ' . $e->getMessage());
        }
        
        return redirect()->route('cancelacion.exito')
            ->with('reserva', $reserva);
    }
    
    /**
     * Mostrar página de éxito
     */
    public function exito()
    {
        if (!session()->has('reserva')) {
            return redirect()->route('home');
        }
        
        $reserva = session('reserva');
        return view('reservas.cancelacion-exitosa', compact('reserva'));
    }
}