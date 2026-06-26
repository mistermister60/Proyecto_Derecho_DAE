<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CasoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AgendaController;

// Ruta raíz redirige al dashboard
Route::get("/", function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Casos
Route::get('/casos', [CasoController::class, 'index'])->name('casos.index');
Route::get('/casos/crear', [CasoController::class, 'create'])->name('casos.create');
Route::get('/casos/{expediente}', [CasoController::class, 'show'])->name('casos.show');
Route::get('/casos/{expediente}/reasignar', [CasoController::class, 'reasignar'])->name('casos.reasignar');

// Clientes
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
Route::get('/clientes/{identidad}', [ClienteController::class, 'show'])->name('clientes.show');

// Agenda
Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
