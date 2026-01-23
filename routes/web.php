<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\CancelacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminReservaController;
use App\Http\Controllers\Admin\RecintoController;
use App\Http\Controllers\Admin\EventoController;
use App\Http\Controllers\Admin\EstadisticasController;
use App\Http\Controllers\Admin\IncidenciasController;
use App\Http\Controllers\Admin\AuditoriaController;
use App\Http\Controllers\Admin\ReporteOrganizacionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Middleware\EnsureUserIsJefeRecintos;
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

// API para obtener el estado de un día (disponible/ocupado/mantenimiento)
Route::get('/api/estado-dia', [CalendarioController::class, 'estadoDia'])
    ->name('api.estadoDia');

// API para obtener estados de todos los días de un mes (optimizado)
Route::get('/api/estados-mes', [CalendarioController::class, 'estadosMes'])
    ->name('api.estadosMes');

// Página de Reglamentos
Route::get('/reglamentos', function () {
    return view('reglamentos.index');
})->name('reglamentos');

// Página Segunda Etapa
Route::get('/segunda-etapa', function () {
    return view('segunda-etapa.index');
})->name('segunda-etapa');



/*
|--------------------------------------------------------------------------
| Recintos (Protegido - SOLO JEFE DE RECINTOS) IMPORTANTE! 
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', EnsureUserIsJefeRecintos::class])
    ->get('/recintos', [RecintoController::class, 'index'])
    ->name('admin.recintos.index');

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
| Recuperación de Contraseña
|--------------------------------------------------------------------------
*/

// Formulario para solicitar recuperación de contraseña
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->middleware('guest')
    ->name('password.request');

// Enviar email con link de recuperación
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->middleware('guest')
    ->name('password.email');

// Formulario para establecer nueva contraseña
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])
    ->middleware('guest')
    ->name('password.reset');

// Procesar nueva contraseña
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])
    ->middleware('guest')
    ->name('password.update');

/*
|--------------------------------------------------------------------------
| Panel Administrativo (requiere autenticación)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard principal del administrador
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    // Exportar reservas del dashboard
    Route::get('/dashboard/exportar', [DashboardController::class, 'exportar'])
        ->name('dashboard.exportar');
    
    /*
    |----------------------------------------------------------------------
    | Rutas de Estadísticas y Reportes (SECCIÓN 6)
    |----------------------------------------------------------------------
    */
    
    Route::prefix('estadisticas')->name('estadisticas.')->group(function () {
        // Página principal de estadísticas - Dashboard
        Route::get('/', [EstadisticasController::class, 'index'])
            ->name('index');
        
        // Aplicar filtros (POST)
        Route::post('/aplicar-filtros', [EstadisticasController::class, 'aplicarFiltros'])
            ->name('aplicar-filtros');
        
        // Limpiar filtros (POST)
        Route::post('/limpiar-filtros', [EstadisticasController::class, 'limpiarFiltros'])
            ->name('limpiar-filtros');
        
        // Exportación a Excel
        Route::get('/exportar-excel', [EstadisticasController::class, 'exportarExcel'])
            ->name('exportar-excel');
        
        // Exportación a PDF
        Route::get('/exportar-pdf', [EstadisticasController::class, 'exportarPdf'])
            ->name('exportar-pdf');
    });
    
    /*
    |----------------------------------------------------------------------
    | Reportes - Módulo de Reportes por Organización
    |----------------------------------------------------------------------
    */
    Route::prefix('reportes')->name('reportes.')->group(function () {
        // Vista principal del reporte histórico por organización
        Route::get('/organizacion', [ReporteOrganizacionController::class, 'index'])
            ->name('organizacion');
        
        // API para buscar organizaciones (autocompletado)
        Route::get('/buscar', [ReporteOrganizacionController::class, 'buscarOrganizaciones'])
            ->name('buscar');
        
        // API para generar reporte JSON
        Route::post('/generar', [ReporteOrganizacionController::class, 'generarReporte'])
            ->name('generar');
        
        // Exportar reporte a PDF
        Route::post('/exportar', [ReporteOrganizacionController::class, 'exportarPDF'])
            ->name('exportar');
    });
    
    /*
    |----------------------------------------------------------------------
    | Gestión de Reservas
    |----------------------------------------------------------------------
    */
    
    Route::prefix('reservas')->name('reservas.')->group(function () {
        // Todas las rutas de visualización - Todos pueden ver
        Route::get('/', [AdminReservaController::class, 'index'])
            ->name('index');
        Route::get('/{reserva}', [AdminReservaController::class, 'show'])
            ->name('show');
        
        // RUTAS PROTEGIDAS - SOLO JEFE DE RECINTOS
        Route::middleware([EnsureUserIsJefeRecintos::class])->group(function () {
            Route::post('/{reserva}/aprobar', [AdminReservaController::class, 'aprobar'])
                ->name('aprobar');
            Route::post('/{reserva}/rechazar', [AdminReservaController::class, 'rechazar'])
                ->name('rechazar');
        });
    });

    /*
    |----------------------------------------------------------------------
    | Gestión de Recintos (Admin) - SOLO JEFE DE RECINTOS Y ADMINS
    |----------------------------------------------------------------------
    */
    
    Route::middleware([EnsureUserIsJefeRecintos::class])->prefix('recintos')->name('recintos.')->group(function () {
        Route::get('/crear', [RecintoController::class, 'create'])
            ->name('create');
        Route::post('/', [RecintoController::class, 'store'])
            ->name('store');
        Route::get('/{recinto}/editar', [RecintoController::class, 'edit'])
            ->name('edit');
        Route::put('/{recinto}', [RecintoController::class, 'update'])
            ->name('update');
        Route::delete('/{recinto}', [RecintoController::class, 'destroy'])
            ->name('destroy');
        Route::post('/{recinto}/cambiar-estado', [RecintoController::class, 'cambiarEstado'])
            ->name('cambiar-estado');
    });

    /*
    |----------------------------------------------------------------------
    | Gestión de Eventos (Carrusel) - SOLO JEFE DE RECINTOS Y ADMINS
    |----------------------------------------------------------------------
    */
    
    Route::middleware([EnsureUserIsJefeRecintos::class])->prefix('eventos')->name('eventos.')->group(function () {
        Route::get('/', [EventoController::class, 'index'])
            ->name('index');
        Route::get('/crear', [EventoController::class, 'create'])
            ->name('create');
        Route::post('/', [EventoController::class, 'store'])
            ->name('store');
        Route::get('/{evento}/editar', [EventoController::class, 'edit'])
            ->name('edit');
        Route::put('/{evento}', [EventoController::class, 'update'])
            ->name('update');
        Route::delete('/{evento}', [EventoController::class, 'destroy'])
            ->name('destroy');
    });

    /*
    |----------------------------------------------------------------------
    | Gestión de Incidencias - Todos los usuarios autenticados
    |----------------------------------------------------------------------
    */
    
    Route::prefix('incidencias')->name('incidencias.')->group(function () {
        Route::get('/', [IncidenciasController::class, 'index'])
            ->name('index');
        Route::get('/crear/{reservaId}', [IncidenciasController::class, 'crear'])
            ->name('crear');
        Route::post('/guardar/{reservaId}', [IncidenciasController::class, 'store'])
            ->name('store');
        Route::get('/{incidencia}', [IncidenciasController::class, 'show'])
            ->name('show');
        Route::post('/{incidencia}/cambiar-estado', [IncidenciasController::class, 'cambiarEstado'])
            ->name('cambiar-estado');
        Route::delete('/{incidencia}', [IncidenciasController::class, 'destroy'])
            ->name('destroy');
    });
    
    /*
    |----------------------------------------------------------------------
    | Auditoría - SOLO JEFE DE RECINTOS
    |----------------------------------------------------------------------
    */
    
    Route::middleware([EnsureUserIsJefeRecintos::class])->prefix('auditoria')->name('auditoria.')->group(function () {
        // Listado de logs de auditoría
        Route::get('/', [AuditoriaController::class, 'index'])
            ->name('index');
        
        // Exportar logs de auditoría
        Route::get('/exportar', [AuditoriaController::class, 'exportar'])
            ->name('exportar');
        
        // Detalles de un log específico
        Route::get('/{log}', [AuditoriaController::class, 'show'])
            ->name('show');
    });
    
});