<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservaCancelada;

/**
 * Controlador de Cancelacion de Reservas
 * 
 * Permite a los ciudadanos cancelar sus reservas aprobadas.
 * Para cancelar, el ciudadano necesita el codigo unico que recibio
 * por correo cuando su reserva fue aprobada.
 */
class CancelacionController extends Controller
{
    /**
     * Muestra el formulario donde el ciudadano ingresa su codigo de cancelacion
     * 
     * Esta es la pagina principal de cancelacion, accesible desde el menu.
     */
    public function mostrarFormulario()
    {
        return view('reservas.cancelar');
    }
    
    /**
     * Busca la reserva usando el codigo ingresado por el ciudadano
     * 
     * Si el codigo es valido y la reserva puede cancelarse, muestra
     * una pagina de confirmacion con los detalles de la reserva.
     */
    public function buscarReserva(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string'
        ]);
        
        // Normaliza el codigo a mayusculas y sin espacios
        $codigo = strtoupper(trim($request->codigo));
        $reserva = Reserva::buscarPorCodigo($codigo);
        
        if (!$reserva) {
            return redirect()->route('cancelacion.formulario')
                ->withInput()
                ->withErrors(['codigo' => 'El código ingresado no es válido o no existe.']);
        }
        
        // Verifica si la reserva puede ser cancelada
        if (!$reserva->esCancelable()) {
            $mensaje = 'Esta reserva no puede ser cancelada. ';
            
            // Explica al ciudadano por que no puede cancelar
            if ($reserva->estado === 'cancelada') {
                $mensaje .= 'La reserva ya fue cancelada anteriormente.';
            } elseif ($reserva->estado === 'rechazada') {
                $mensaje .= 'La reserva fue rechazada y no requiere cancelación.';
            } elseif ($reserva->estado === 'pendiente') {
                $mensaje .= 'La reserva aún está pendiente de aprobación.';
            } else {
                $mensaje .= 'La fecha de la reserva ya pasó.';
            }
            
            return redirect()->route('cancelacion.formulario')
                ->withInput()
                ->withErrors(['codigo' => $mensaje]);
        }
        
        // Carga los datos del recinto para mostrar en la confirmacion
        $reserva->load('recinto');
        
        // Muestra la pagina de confirmacion con los detalles de la reserva
        return view('reservas.confirmar-cancelacion', compact('reserva'));
    }
    
    /**
     * Procesa la cancelacion de la reserva
     * 
     * Cuando el ciudadano confirma que quiere cancelar:
     * 1. Actualiza la reserva como cancelada
     * 2. Envia un correo de confirmacion
     * 3. Redirige a la pagina de exito
     */
    public function cancelar(Request $request, $codigo)
    {
        // El motivo es obligatorio para saber por que el ciudadano cancela
        $request->validate([
            'motivo' => 'required|string|max:500'
        ]);
        
        $reserva = Reserva::buscarPorCodigo($codigo);
        
        if (!$reserva) {
            return redirect()->route('cancelacion.formulario')
                ->withErrors(['error' => 'Reserva no encontrada.']);
        }
        
        // Verifica nuevamente que se pueda cancelar (seguridad adicional)
        if (!$reserva->esCancelable()) {
            return redirect()->route('cancelacion.formulario')
                ->withErrors(['error' => 'Esta reserva no puede ser cancelada.']);
        }
        
        // Ejecuta la cancelacion con el motivo indicado
        $reserva->cancelarPorUsuario($request->motivo);
        
        // Envia correo de confirmacion de cancelacion al ciudadano
        try {
            Mail::to($reserva->email)->send(new ReservaCancelada($reserva));
        } catch (\Exception $e) {
            \Log::error('Error enviando correo de cancelación: ' . $e->getMessage());
        }
        
        // Guarda la reserva en sesion para mostrarla en la pagina de exito
        return redirect()->route('cancelacion.exito')
            ->with('reserva', $reserva);
    }
    
    /**
     * Muestra la pagina de cancelacion exitosa
     * 
     * Confirma al ciudadano que su reserva fue cancelada
     * y muestra un resumen de la reserva cancelada.
     */
    public function exito()
    {
        // Si no hay reserva en sesion, redirige al inicio
        if (!session()->has('reserva')) {
            return redirect()->route('home');
        }
        
        $reserva = session('reserva');
        return view('reservas.cancelacion-exitosa', compact('reserva'));
    }
}