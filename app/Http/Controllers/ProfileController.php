<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
/**ESTE CONTRALADOR NO SE HA IMPLEMENTADO, SOLO ES UN BOSQUEJO PARA EDITAR PERFILES REGISTRADOS - SEGUNDA ETAPA */

class ProfileController extends Controller
{
    /** - Renderiza la vista `Profile/Edit` usando Inertia
       * - Envía dos datos al frontend:
       * - `mustVerifyEmail`: verifica si el usuario necesita confirmar su email
       * - `status`: mensajes de sesión (ej: "Perfil actualizado exitosamente")
       * FORMULARIO DE EDITAR PERFIL
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**- Valida los datos con `ProfileUpdateRequest` (validación personalizada)
    *- Actualiza los campos del usuario con `fill($request->validated())`
    *- Lógica especial de email: Si el usuario cambia su email, resetea `email_verified_at` a `null` (requiere re-verificación)
    *- Guarda los cambios
    *- Redirige de vuelta a `profile.edit`
     * ACTUALIZAR PERFIL
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**- Valida la contraseña con `current_password`
    *- Obtiene el usuario autenticado
    *- Cierra la sesión del usuario (`Auth::logout()`)
    *- Elimina la cuenta del usuario (`$user->delete()`)
    *- Invalida la sesión y regenera el token
    *- Redirige a la página principal
     * ELIMINAR PERFIL
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
/* Características de seguridad
- Validación de contraseña antes de eliminar cuenta
- Re-verificación de email al cambiar correo
- Invalidación de sesión tras eliminación
- Uso de Form Requests para validación estructurada
*/