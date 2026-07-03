<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CasoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemandadoController;
use App\Http\Controllers\ProcuradorController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\UsuariosController;

// Ruta raíz redirige al dashboard (o login si no autenticado)
Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

// Autenticación (públicas)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Casos
    Route::get('/casos/crear', [CasoController::class, 'create'])->name('casos.create');
    Route::post('/casos', [CasoController::class, 'store'])->name('casos.store');
    Route::get('/casos/{expediente}/editar', [CasoController::class, 'edit'])->name('casos.edit');
    Route::put('/casos/{expediente}', [CasoController::class, 'update'])->name('casos.update');
Route::middleware('role:Director')->group(function () {
    Route::delete('/casos/{expediente}', [CasoController::class, 'destroy'])->name('casos.destroy');
    Route::get('/casos/{expediente}/reasignar', [CasoController::class, 'reasignar'])->name('casos.reasignar');
    Route::post('/casos/{expediente}/reasignar', [CasoController::class, 'storeReasignacion'])->name('casos.storeReasignacion');
    });
    Route::get('/casos/{expediente}', [CasoController::class, 'show'])->name('casos.show');
    Route::get('/casos', [CasoController::class, 'index'])->name('casos.index');

    // Clientes
    Route::get('/clientes/crear', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{identidad}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{identidad}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{identidad}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
    Route::post('/clientes/{identidad}/activar', [ClienteController::class, 'activar'])->name('clientes.activar');
    Route::get('/clientes/{identidad}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');

    // Usuarios (solo Director)
    Route::middleware('role:Director')->group(function () {
        Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/crear', [UsuariosController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuariosController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{id}/editar', [UsuariosController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{id}', [UsuariosController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{id}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');
        Route::post('/usuarios/{id}/activar', [UsuariosController::class, 'activar'])->name('usuarios.activar');
        Route::get('/usuarios/{id}', [UsuariosController::class, 'show'])->name('usuarios.show');
    });

    // Demandados
    Route::get('/demandados', [DemandadoController::class, 'index'])->name('demandados.index');
    Route::get('/demandados/crear', [DemandadoController::class, 'create'])->name('demandados.create');
    Route::post('/demandados', [DemandadoController::class, 'store'])->name('demandados.store');
    Route::get('/demandados/{identidad}/editar', [DemandadoController::class, 'edit'])->name('demandados.edit');
    Route::put('/demandados/{identidad}', [DemandadoController::class, 'update'])->name('demandados.update');
    Route::delete('/demandados/{identidad}', [DemandadoController::class, 'destroy'])->name('demandados.destroy');
    Route::post('/demandados/{identidad}/activar', [DemandadoController::class, 'activar'])->name('demandados.activar');
    Route::get('/demandados/{identidad}', [DemandadoController::class, 'show'])->name('demandados.show');

    // Procuradores (solo Director)
    Route::middleware('role:Director')->group(function () {
        Route::get('/procuradores', [ProcuradorController::class, 'index'])->name('procuradores.index');
        Route::get('/procuradores/crear', [ProcuradorController::class, 'create'])->name('procuradores.create');
        Route::post('/procuradores', [ProcuradorController::class, 'store'])->name('procuradores.store');
        Route::get('/procuradores/{identidad}/editar', [ProcuradorController::class, 'edit'])->name('procuradores.edit');
        Route::put('/procuradores/{identidad}', [ProcuradorController::class, 'update'])->name('procuradores.update');
        Route::delete('/procuradores/{identidad}', [ProcuradorController::class, 'destroy'])->name('procuradores.destroy');
        Route::post('/procuradores/{identidad}/activar', [ProcuradorController::class, 'activar'])->name('procuradores.activar');
        Route::get('/procuradores/{identidad}', [ProcuradorController::class, 'show'])->name('procuradores.show');
    });

    // Agenda
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');

    // Seguimiento
    Route::post('/casos/{caso_id}/seguimiento', [SeguimientoController::class, 'store'])->name('seguimientos.store');
});
