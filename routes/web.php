<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\CancelacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminReservaController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (sin autenticación requerida)
|--------------------------------------------------------------------------
*/

// Página principal - Inicio con carrusel y calendarios
Route::get('/', [HomeController::class, 'index'])->name('home');

// Calendario de disponibilidad semanal
Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario');

// API para obtener disponibilidad de un recinto en una fecha (usada por JavaScript)
Route::get('/api/disponibilidad', [CalendarioController::class, 'disponibilidad'])
    ->name('api.disponibilidad');

// Reservas públicas
Route::get('/reservas/crear/{recinto}', [ReservaController::class, 'create'])
    ->name('reservas.create');
Route::post('/reservas', [ReservaController::class, 'store'])
    ->name('reservas.store');
Route::get('/reservas/{reserva}', [ReservaController::class, 'show'])
    ->name('reservas.show');

/*
|--------------------------------------------------------------------------
| Rutas de Cancelación de Reservas (públicas)
|--------------------------------------------------------------------------
*/

// Formulario para ingresar código de cancelación
Route::get('/cancelar-reserva', [CancelacionController::class, 'mostrarFormulario'])
    ->name('cancelacion.formulario');

// Buscar reserva por código
Route::post('/cancelar-reserva/buscar', [CancelacionController::class, 'buscarReserva'])
    ->name('cancelacion.buscar');

// Procesar cancelación
Route::post('/cancelar-reserva/{codigo}', [CancelacionController::class, 'cancelar'])
    ->name('cancelacion.procesar');

// Página de cancelación exitosa
Route::get('/cancelacion-exitosa', [CancelacionController::class, 'exito'])
    ->name('cancelacion.exito');

/*
|--------------------------------------------------------------------------
| Autenticación Administrativa
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Panel Administrativo (requiere autenticación)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard principal del administrador
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    // Estadísticas
    Route::get('/estadisticas', [\App\Http\Controllers\Admin\EstadisticasController::class, 'index'])
        ->name('estadisticas.index');
    
    // Gestión de reservas
    Route::get('/reservas', [AdminReservaController::class, 'index'])
        ->name('reservas.index');
    Route::get('/reservas/{reserva}', [AdminReservaController::class, 'show'])
        ->name('reservas.show');
    Route::post('/reservas/{reserva}/aprobar', [AdminReservaController::class, 'aprobar'])
        ->name('reservas.aprobar');
    Route::post('/reservas/{reserva}/rechazar', [AdminReservaController::class, 'rechazar'])
        ->name('reservas.rechazar');
});