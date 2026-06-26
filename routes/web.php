<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CasoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AgendaController;

// Ruta raíz redirige al dashboard (o login si no autenticado)
Route::get("/", function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

// Autenticación (públicas)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
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
    Route::delete('/casos/{expediente}', [CasoController::class, 'destroy'])->name('casos.destroy');
    Route::get('/casos/{expediente}/reasignar', [CasoController::class, 'reasignar'])->name('casos.reasignar');
    Route::post('/casos/{expediente}/reasignar', [CasoController::class, 'storeReasignacion'])->name('casos.storeReasignacion');
    Route::get('/casos/{expediente}', [CasoController::class, 'show'])->name('casos.show');
    Route::get('/casos', [CasoController::class, 'index'])->name('casos.index');

    // Clientes
    Route::get('/clientes/crear', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{identidad}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{identidad}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{identidad}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
    Route::get('/clientes/{identidad}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');

    // Agenda
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
});
