<?php

namespace App\Http\Controllers;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: AuthController
 * ═══════════════════════════════════════════════════════
 * Gestiona la autenticación de usuarios del sistema DAE.
 * Atiende las rutas: login, 2FA (OTP por email), logout.
 * Middleware: 'guest' para showLogin/login, 'auth' para logout.
 * Roles: accesible para cualquier usuario con credenciales.
 * Delega la lógica de autenticación en AuthService.
 * El superadmin omite el 2FA automáticamente.
 */

use App\Exceptions\AuthenticationException;
use App\Http\Requests\LoginCredentialsRequest;
use App\Mail\CodigoVerificacionMail;
use App\Services\AuthService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

/**
 * Controlador para la autenticación de usuarios en el sistema DAE.
 *
 * Gestiona el inicio y cierre de sesión delegando la lógica de autenticación
 * en AuthService. Incluye manejo de errores de credenciales inválidas.
 */
class AuthController extends BaseController
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Muestra el formulario de inicio de sesión.
     *
     * @return Response Vista con el formulario login
     */
    public function showLogin(): Response
    {
        return response()->view('auth.login');
    }

    /**
     * Procesa el inicio de sesión con credenciales.
     *
     * Delega la autenticación en AuthService::attemptLogin(). En caso de
     * credenciales inválidas redirige de vuelta con errores en el campo email.
     *
     * @param  LoginCredentialsRequest  $request  Validación de email y contraseña
     * @return RedirectResponse Redirección al dashboard o retroceso con errores
     *
     * @throws AuthenticationException Capturada internamente; no propaga
     */
    public function login(LoginCredentialsRequest $request): RedirectResponse
    {
        try {
            $this->authService->attemptLogin(
                $request->input('email'),
                $request->input('contrasena')
            );

            $user = auth()->user();

            // ─── [Super Admin: omite 2FA automáticamente] ────
            // Si el email coincide con el configurado como super_admin
            // en config/auth.php, se salta el paso de verificación 2FA
            if ($user && $user->email === config('auth.super_admin_email')) {
                Session::put('two_factor_verified', true);

                // Verificar si debe cambiar contraseña (primer login)
                if ($user->debe_cambiar_contrasena) {
                    return redirect()->route('password.change');
                }

                return redirect()->intended(route('dashboard'));
            }

            // ─── [Generación del código 2FA] ──────────────────
            // Crea un código aleatorio seguro de 6 dígitos para
            // el segundo factor de autenticación
            $codigo2FA = random_int(100000, 999999);

            // 2. Guardamos los datos temporalmente en la sesión para validarlos después
            session([
                'two_factor_code' => $codigo2FA,
                'two_factor_expires_at' => Carbon::now()->addMinutes(15),
                'two_factor_email' => $request->input('email'),
            ]);

            // 3. Enviamos el correo real utilizando el módulo que creaste
            Mail::to($request->input('email'))->send(new CodigoVerificacionMail($codigo2FA));

            // 4. Redirigimos al usuario a la vista para escribir el código
            return redirect()->route('auth.two-factor')->with('success', 'Código de verificación enviado a tu correo institucional.');

        } catch (AuthenticationException $e) {
            return back()->withErrors([
                'email' => $e->getMessage(),
            ])->onlyInput('email');
        }
    }

    /**
     * ═══════════════════════════════════════════════════
     * verifyTwoFactor
     * ───────────────────────────────────────────────────
     * Verifica el código OTP de 6 dígitos enviado al correo
     * del usuario durante el 2FA. Comprueba que coincida con
     * el almacenado en sesión y que no haya expirado (15 min).
     * Si es válido, marca la sesión como verificada y redirige
     * al dashboard. En caso contrario, retorna error al formulario.
     * ═══════════════════════════════════════════════════
     */
    public function verifyTwoFactor(Request $request)
    {
        // ─── [Validación del código ingresado] ────────────────
        // El código debe ser un valor numérico obligatorio
        $request->validate([
            'code' => 'required|numeric',
        ]);

        // ─── [Verificación del código contra la sesión] ──────
        // Comprueba coincidencia exacta y que la fecha actual
        // esté dentro del margen de 15 minutos desde su generación
        if ($request->input('code') == session('two_factor_code') &&
            Carbon::now()->isBefore(session('two_factor_expires_at'))) {

            // ─── [Código válido: limpiar sesión y autorizar] ─
            // Elimina los datos temporales del 2FA y marca al
            // usuario como verificado para el resto de la sesión
            session()->forget(['two_factor_code', 'two_factor_expires_at']);
            session(['two_factor_verified' => true]);

            return redirect()->intended(route('dashboard'));
        }

        // ─── [Código inválido o expirado] ────────────────────
        // Devuelve error sin revelar si el código era incorrecto
        // o ya expiró, por seguridad
        return back()->withErrors(['code' => 'El código de verificación es inválido o ha expirado.']);
    }

    /**
     * Cierra la sesión del usuario.
     *
     * Revoca el token de autenticación via AuthService, cierra la sesión
     * de Laravel con Auth::logout(), invalida la sesión y regenera el token CSRF.
     *
     * @return RedirectResponse Redirección al formulario de login
     */
    public function logout(): RedirectResponse
    {
        // ─── [Revocación del token en AuthService] ──────────
        // Intenta revocar el token Sanctum del usuario; si falla
        // (por ejemplo, si el usuario ya no tiene token), se
        // captura silenciosamente para no interrumpir el cierre
        try {
            $this->authService->logout(auth()->id());
        } catch (\Throwable) {
            // Silently handle token revocation errors
        }

        // ─── [Cierre de sesión de Laravel] ──────────────────
        // Limpia la sesión, invalida el ID de sesión actual
        // y regenera el token CSRF por seguridad
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route('login');
    }
}
