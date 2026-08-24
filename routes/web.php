<?php

/**
 * ═══════════════════════════════════════════════════════
 * ARCHIVO DE RUTAS WEB
 * ═══════════════════════════════════════════════════════
 * Define TODAS las rutas del sistema organizadas por:
 * - Autenticación (login/logout/2FA)
 * - Recuperación de contraseña
 * - Recursos protegidos (Casos, Clientes, etc.)
 * - PWA (Service Worker, notificaciones push)
 * - Búsqueda global
 * ───────────────────────────────────────────────────────
 * Middleware pipeline: auth → otp → password.changed
 * Rol: Director (rol_id=1), Procurador (rol_id=2)
 */

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AudienciaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CasoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemandadoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\EntrevistaController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PerfilProcuradorController;
use App\Http\Controllers\ProcuradorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

// ═══════════════════════════════════════════════════════
// RUTAS PÚBLICAS — Sin autenticación
// ═══════════════════════════════════════════════════════
// Redirección raíz y archivos estáticos (PWA: manifest, offline, logo)

// Ruta raíz redirige al dashboard (o login si no autenticado)
Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

// Health check para Service Worker
Route::get('/api/health', function () {
    return response()->noContent();
});

// ═══════════════════════════════════════════════════════
// ARCHIVOS ESTÁTICOS PWA
// ═══════════════════════════════════════════════════════
// Se sirven desde PHP para poder aplicar cabeceras de caché.
// sw.js se sirve directamente desde public/ (sin closure PHP).

// Archivos estáticos PWA (para servir en test y producción)
Route::get('/manifest.json', function () {
    $content = file_get_contents(public_path('manifest.json'));

    return response($content, 200, [
        'Content-Type' => 'application/json',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->name('pwa.manifest');

// /sw.js se sirve directamente desde public/sw.js (sin closure PHP)

Route::get('/offline.html', function () {
    $content = file_get_contents(public_path('offline.html'));

    return response($content, 200, [
        'Content-Type' => 'text/html',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->name('pwa.offline');

Route::get('/logo.svg', function () {
    $content = file_get_contents(public_path('logo.svg'));

    return response($content, 200, [
        'Content-Type' => 'image/svg+xml',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->name('pwa.logo_svg');

Route::get('/logo.png', function () {
    $content = file_get_contents(public_path('logo.png'));

    return response($content, 200, [
        'Content-Type' => 'image/png',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->name('pwa.logo_png');

// ═══════════════════════════════════════════════════════
// AUTENTICACIÓN — Login / Logout
// ═══════════════════════════════════════════════════════
// Rutas públicas (sin middleware).
// Login con rate limiting (5 intentos → bloqueo 5 min).
// Logout destruye sesión y revoca token Sanctum.
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ═══════════════════════════════════════════════════════
// RECUPERACIÓN DE CONTRASEÑA — Auto-servicio por email
// ═══════════════════════════════════════════════════════
// Rutas públicas. El usuario solicita un token por correo
// y luego restablece su contraseña.
Route::get('/olvide-mi-contrasena', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/olvide-mi-contrasena', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('throttle:10,1');

Route::get('/restablecer-contrasena/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/restablecer-contrasena', [ResetPasswordController::class, 'reset'])->name('password.update')->middleware('throttle:10,1');

// ═══════════════════════════════════════════════════════
// CONFIRMACIÓN DE CONTRASEÑA — Acciones sensibles
// ═══════════════════════════════════════════════════════
// Protegida con middleware 'auth' (no requiere 2FA ni cambio de password).
// Se pide antes de eliminar registros o cambiar datos críticos.
// La sesión se marca como "password_confirmed_at" con límite de tiempo.
Route::middleware('auth')->group(function () {
    Route::get('/confirmar-contrasena', function () {
        return view('auth.confirm-password');
    })->name('password.confirm');

    Route::post('/confirmar-contrasena', function (Request $request) {
        $request->validate(['password' => 'required']);

        if (! Hash::check($request->password, $request->user()->contrasena)) {
            return back()->withErrors(['password' => 'La contraseña no coincide.']);
        }

        $request->user()->confirmPassword();

        return redirect()->intended(route('dashboard'))->with('success', 'Contraseña confirmada.');
    })->name('password.confirm.store');
});

// ═══════════════════════════════════════════════════════
// VERIFICACIÓN EN DOS PASOS (2FA) — OTP por email
// ═══════════════════════════════════════════════════════
// Públicas (sin middleware). Se accede tras login exitoso.
// El usuario ingresa un código de 6 dígitos enviado a su correo.
// El Director (rol_id=1) omite este paso automáticamente.
Route::get('/verify-two-factor', function () {
    return view('auth.two-factor');
})->name('auth.two-factor');

Route::post('/verify-two-factor', [AuthController::class, 'verifyTwoFactor'])->name('auth.two-factor.verify')->middleware('throttle:10,1');

// ═══════════════════════════════════════════════════════
// PERFIL DE USUARIO
// ═══════════════════════════════════════════════════════
// Protegido con auth + otp + password.changed.
// Edición de datos personales, cierre de cuenta y cambio de contraseña.
Route::middleware(['auth', 'otp', 'password.changed'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cambio de contraseña desde perfil (validate current password)
    Route::put('/profile/password', function (Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        if (! Hash::check($request->current_password, $request->user()->contrasena)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no coincide.']);
        }

        $request->user()->contrasena = Hash::make($request->password);
        $request->user()->save();

        return back()->with('status', 'password-updated');
    })->name('profile.password.update')->middleware('throttle:10,1');
});

// ═══════════════════════════════════════════════════════
// RUTAS PROTEGIDAS (auth + otp + password.changed)
// ═══════════════════════════════════════════════════════
// Pipeline completo: autenticado, 2FA verificado, contraseña ya cambiada.
// Agrupa todos los recursos del sistema: Casos, Clientes, Demandados,
// Procuradores, Usuarios, Agenda, Audiencias, Documentos, etc.
// ───────────────────────────────────────────────────────
// Middleware adicional:
//   - role:Director  → solo Director (rol_id=1)
//   - role:Procurador→ solo Procurador (rol_id=2)
Route::middleware(['auth', 'otp', 'password.changed', 'profile.complete'])->group(function () {

    // ═══════════════════════════════════════════════════════
    // CAMBIO OBLIGATORIO DE CONTRASEÑA
    // ═══════════════════════════════════════════════════════
    // El middleware password.changed permite explícitamente estas rutas
    // para que el usuario pueda cambiar su contraseña en el primer inicio.
    Route::get('/cambiar-contrasena', [PasswordChangeController::class, 'showChangeForm'])->name('password.change');
    Route::post('/cambiar-contrasena', [PasswordChangeController::class, 'update'])->name('password.change.update');

    // ═══════════════════════════════════════════════════════
    // COMPLETAR PERFIL (PROCURADOR) — Primer inicio de sesión
    // ═══════════════════════════════════════════════════════
    // El procurador debe registrar DNI, fecha de nacimiento, celular y
    // contacto de emergencia antes de usar el sistema. El middleware
    // profile.complete permite explícitamente estas rutas.
    Route::get('/procuradores/completar-perfil', [PerfilProcuradorController::class, 'show'])->name('procuradores.completar-perfil');
    Route::post('/procuradores/completar-perfil', [PerfilProcuradorController::class, 'store'])->name('procuradores.completar-perfil.store');

    // ═══════════════════════════════════════════════════════
    // DASHBOARD — Panel principal
    // ═══════════════════════════════════════════════════════
    // Estadísticas generales del consultorio: casos activos,
    // próximos vencimientos, gráficos de carga laboral.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ═══════════════════════════════════════════════════════
    // CASOS — CRUD + Kanban + Reasignación + PDF
    // ═══════════════════════════════════════════════════════
    // Recurso principal del sistema. Cada caso tiene un expediente único.
    // Acciones destructivas y reasignación requieren rol Director.
    Route::get('/casos/crear', [CasoController::class, 'create'])->name('casos.create');
    Route::post('/casos', [CasoController::class, 'store'])->name('casos.store');
    Route::get('/casos/{expediente}/editar', [CasoController::class, 'edit'])->name('casos.edit');
    Route::put('/casos/{expediente}', [CasoController::class, 'update'])->name('casos.update');
    Route::middleware('role:Director')->group(function () {
        Route::delete('/casos/{expediente}', [CasoController::class, 'destroy'])->name('casos.destroy');
        Route::get('/casos/{expediente}/cerrar', [CasoController::class, 'cerrar'])->name('casos.cerrar');
        Route::post('/casos/{expediente}/cerrar', [CasoController::class, 'storeCerrar'])->name('casos.storeCerrar');
        Route::get('/casos/{expediente}/reasignar', [CasoController::class, 'reasignar'])->name('casos.reasignar');
        Route::post('/casos/{expediente}/reasignar', [CasoController::class, 'storeReasignacion'])->name('casos.storeReasignacion');

    });
    Route::get('/casos/{expediente}', [CasoController::class, 'show'])->name('casos.show');
    Route::get('/casos/{expediente}/pdf-seguimiento', [PDFController::class, 'seguimiento'])->name('casos.pdf-seguimiento');

    Route::get('/casos', [CasoController::class, 'index'])->name('casos.index');

    // ───────────────────────────────────────────────────────
    // CLIENTES — CRUD completo
    // ───────────────────────────────────────────────────────
    // Personas representadas por el consultorio (catálogo compartido:
    // Director y Procurador consultan y crean). La clave primaria es
    // 'identidad' (DNI). Modificar/desactivar registros: solo Director.
    // La API /clientes/buscar DEBE ir antes de /clientes/{identidad}
    // para no ser capturada como show.
    Route::get('/clientes/crear', [ClienteController::class, 'create'])->name('clientes.create');
    Route::get('/clientes/buscar', [ClienteController::class, 'search'])->name('clientes.search');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{identidad}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::middleware('role:Director')->group(function () {
        Route::get('/clientes/{identidad}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('/clientes/{identidad}', [ClienteController::class, 'update'])->name('clientes.update');
        Route::delete('/clientes/{identidad}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
        Route::post('/clientes/{identidad}/activar', [ClienteController::class, 'activar'])->name('clientes.activar');
    });

    // ═══════════════════════════════════════════════════════
    // USUARIOS DEL SISTEMA — Solo Director
    // ═══════════════════════════════════════════════════════
    // CRUD completo de usuarios. Solo accesible por Director (rol_id=1).
    // Incluye activar/desactivar cuentas y resetear contraseña.
    Route::middleware('role:Director')->group(function () {
        Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/crear', [UsuariosController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuariosController::class, 'store'])->name('usuarios.store')->middleware('throttle:10,1');
        Route::get('/usuarios/{id}/editar', [UsuariosController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{id}', [UsuariosController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{id}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');
        Route::post('/usuarios/{id}/activar', [UsuariosController::class, 'activar'])->name('usuarios.activar');
        Route::post('/usuarios/{id}/reset-password', [UsuariosController::class, 'resetPassword'])->name('usuarios.reset-password');
        Route::get('/usuarios/{id}', [UsuariosController::class, 'show'])->name('usuarios.show');
    });

    // ───────────────────────────────────────────────────────
    // DEMANDADOS — CRUD completo
    // ───────────────────────────────────────────────────────
    // Contraparte del caso. Catálogo compartido: consulta y creación
    // para todos; modificar/desactivar registros: solo Director.
    Route::get('/demandados', [DemandadoController::class, 'index'])->name('demandados.index');
    Route::get('/demandados/crear', [DemandadoController::class, 'create'])->name('demandados.create');
    Route::post('/demandados', [DemandadoController::class, 'store'])->name('demandados.store');
    Route::get('/demandados/{identidad}', [DemandadoController::class, 'show'])->name('demandados.show');
    Route::middleware('role:Director')->group(function () {
        Route::get('/demandados/{identidad}/editar', [DemandadoController::class, 'edit'])->name('demandados.edit');
        Route::put('/demandados/{identidad}', [DemandadoController::class, 'update'])->name('demandados.update');
        Route::delete('/demandados/{identidad}', [DemandadoController::class, 'destroy'])->name('demandados.destroy');
        Route::post('/demandados/{identidad}/activar', [DemandadoController::class, 'activar'])->name('demandados.activar');
    });

    // ═══════════════════════════════════════════════════════
    // PROCURADORES — Solo Director
    // ═══════════════════════════════════════════════════════
    // Gestión de procuradores (practicantes/abogados). Solo Director.
    // Incluye generación de constancia de práctica en PDF.
    Route::middleware('role:Director')->group(function () {
        Route::get('/procuradores', [ProcuradorController::class, 'index'])->name('procuradores.index');
        Route::get('/procuradores/crear', [ProcuradorController::class, 'create'])->name('procuradores.create');
        Route::post('/procuradores', [ProcuradorController::class, 'store'])->name('procuradores.store')->middleware('throttle:10,1');
        Route::get('/procuradores/{identidad}/editar', [ProcuradorController::class, 'edit'])->name('procuradores.edit');
        Route::put('/procuradores/{identidad}', [ProcuradorController::class, 'update'])->name('procuradores.update');
        Route::delete('/procuradores/{identidad}', [ProcuradorController::class, 'destroy'])->name('procuradores.destroy');
        Route::post('/procuradores/{identidad}/activar', [ProcuradorController::class, 'activar'])->name('procuradores.activar');
        Route::get('/procuradores/{identidad}', [ProcuradorController::class, 'show'])->name('procuradores.show');
        Route::get('/procuradores/{identidad}/constancia', [PDFController::class, 'constanciaPracticante'])->name('procuradores.constancia');
    });

    // ═══════════════════════════════════════════════════════
    // AGENDA — Calendario general
    // ═══════════════════════════════════════════════════════
    // Vista general de todas las audiencias y eventos próximos.
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');

    // ═══════════════════════════════════════════════════════
    // SEGUIMIENTO — Avances del caso
    // ═══════════════════════════════════════════════════════
    // Registro de seguimiento/progreso vinculado a un caso.
    Route::post('/casos/{caso_id}/seguimiento', [SeguimientoController::class, 'store'])->name('seguimientos.store');

    // ═══════════════════════════════════════════════════════
    // AUDIENCIAS — Anidadas bajo el expediente
    // ═══════════════════════════════════════════════════════
    // Las audiencias pertenecen a un caso y se gestionan desde ahí.
    Route::post('/casos/{expediente}/audiencias', [AudienciaController::class, 'store'])->name('audiencias.store');
    Route::delete('/casos/{expediente}/audiencias/{audiencia_id}', [AudienciaController::class, 'destroy'])->name('audiencias.destroy');

    // ═══════════════════════════════════════════════════════
    // DOCUMENTOS — Anidados bajo el expediente
    // ═══════════════════════════════════════════════════════
    // Subida, descarga y eliminación de documentos del caso.
    Route::post('/casos/{expediente}/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
    Route::get('/casos/{expediente}/documentos/{documento_id}', [DocumentoController::class, 'download'])->name('documentos.download');
    Route::delete('/casos/{expediente}/documentos/{documento_id}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');

    // ═══════════════════════════════════════════════════════
    // ENTREVISTAS — Anidadas bajo el expediente
    // ═══════════════════════════════════════════════════════
    // Registro de entrevistas realizadas con cliente/demandado.
    Route::post('/casos/{expediente}/entrevistas', [EntrevistaController::class, 'store'])->name('entrevistas.store');
    Route::delete('/casos/{expediente}/entrevistas/{entrevista_id}', [EntrevistaController::class, 'destroy'])->name('entrevistas.destroy');

    // ============================================
    // BÚSQUEDA GLOBAL (typeahead)
    // ============================================
    Route::get('/api/search', SearchController::class)->name('api.search');

    // ============================================
    // PWA - Notificaciones Push
    // ============================================
    Route::prefix('api/notifications')->group(function () {
        Route::get('/vapid-public-key', [PwaController::class, 'getVapidPublicKey'])
            ->name('pwa.vapid-key');

        Route::post('/subscribe', [PwaController::class, 'subscribe'])
            ->name('pwa.subscribe');

        Route::post('/unsubscribe', [PwaController::class, 'unsubscribe'])
            ->name('pwa.unsubscribe');

        Route::get('/settings', [PwaController::class, 'notificationSettings'])
            ->name('pwa.notifications');
    });
});
