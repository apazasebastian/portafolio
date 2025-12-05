<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsJefeRecintos
{
    /**
     * Verificar que el usuario sea Jefe de Recintos
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar que el usuario esté autenticado
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Verificar que el rol sea jefe_recintos
        if (auth()->user()->role !== 'jefe_recintos') {
            // Redirigir con mensaje de error
            return redirect()->back()->with('error', 'No tienes permisos para realizar esta acción. Solo el Jefe de Recintos puede aprobar o rechazar reservas.');
        }

        return $next($request);
    }
}