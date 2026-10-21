<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArrendatarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacturaServicioController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ServicioController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard');

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::get('/vencimientos', [FacturaServicioController::class, 'dueIndex'])->name('vencimientos.index');
        Route::resource('arrendatarios', ArrendatarioController::class);
        Route::resource('servicios', ServicioController::class)->except(['show']);
        Route::get('/facturas', [FacturaServicioController::class, 'index'])->name('facturas.index');
        Route::get('/facturas/crear', [FacturaServicioController::class, 'create'])->name('facturas.create');
        Route::post('/facturas', [FacturaServicioController::class, 'store'])->name('facturas.store');
        Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
        Route::get('/pagos/registrar', [PagoController::class, 'create'])->name('pagos.create');
        Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
        Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/arrendatario/{arrendatario}', [ReporteController::class, 'tenant'])
            ->name('reportes.arrendatario');
    });

    Route::prefix('arrendatario')->name('arrendatario.')->middleware('role:arrendatario')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'tenant'])->name('dashboard');
        Route::get('/facturas', [FacturaServicioController::class, 'tenantIndex'])->name('facturas');
        Route::get('/servicios', [ServicioController::class, 'tenantIndex'])->name('servicios');
        Route::get('/pagos', [PagoController::class, 'tenantIndex'])->name('pagos');
        Route::get('/estado-cuenta', [ReporteController::class, 'tenantSelf'])->name('estado-cuenta');
        Route::get('/pagos/{pago}/comprobante', [PagoController::class, 'receipt'])->name('pagos.comprobante');
        Route::get('/historial', [HistorialController::class, 'tenantIndex'])->name('historial');
    });

    Route::get('/pagos/{pago}/comprobante', [PagoController::class, 'receipt'])
        ->name('pagos.comprobante');
});
