<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            //  REGISTRAR LOGIN EXITOSO 
            AuditLog::log(
                'login',
                "Inicio de sesión exitoso"
            );
            
            return redirect()->route('admin.dashboard');
        }

        //  REGISTRAR INTENTO FALLIDO 
        AuditLog::create([
            'user_id' => null,
            'user_name' => 'Intento fallido',
            'user_email' => $request->email,
            'user_role' => 'desconocido',
            'action' => 'acceso_denegado',
            'description' => "Intento de inicio de sesión fallido para: {$request->email}",
            'auditable_type' => null,
            'auditable_id' => null,
            'old_values' => null,
            'new_values' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->withErrors(['email' => 'Credenciales incorrectas']);
    }

    public function logout()
    {
        //  REGISTRAR LOGOUT ANTES DE CERRAR SESIÓN 
        if (Auth::check()) {
            AuditLog::log(
                'logout',
                "Cierre de sesión"
            );
        }
        
        Auth::logout();
        return redirect()->route('calendario');
    }
}