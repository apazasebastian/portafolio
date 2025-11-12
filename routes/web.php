<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminReservaController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (sin autenticación requerida)
|--------------------------------------------------------------------------
*/

// Página principal - Inicio
Route::get('/', [HomeController::class, 'index'])->name('home');

// Calendario de disponibilidad
Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario');

// API para consultar disponibilidad (usado por JavaScript)
Route::get('/calendario/disponibilidad', [CalendarioController::class, 'disponibilidad'])
    ->name('calendario.disponibilidad');

// Reservas públicas
Route::get('/reservas/crear/{recinto}', [ReservaController::class, 'create'])
    ->name('reservas.create');
Route::post('/reservas', [ReservaController::class, 'store'])
    ->name('reservas.store');
Route::get('/reservas/{reserva}', [ReservaController::class, 'show'])
    ->name('reservas.show');

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