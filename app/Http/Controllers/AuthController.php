<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthenticationException;
use App\Http\Requests\LoginCredentialsRequest;
use App\Services\AuthService;
use App\Mail\CodigoVerificacionMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

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
            $authResponse = $this->authService->attemptLogin(
                $request->input('email'),
                $request->input('contrasena')
            );

            $user = auth()->user();

            // Director (super usuario) omite 2FA y va directo al dashboard
            if ($user->rol && $user->rol->rol_nombre === 'Director') {
                session(['two_factor_verified' => true]);
                
                // Verificar si debe cambiar contraseña (primer login)
                if ($user->debe_cambiar_contrasena) {
                    return redirect()->route('password.change');
                }
                
                return redirect()->intended(route('dashboard'));
            }

            // 1. Generamos un código aleatorio de 6 dígitos
            $codigo2FA = rand(100000, 999999);

            // 2. Guardamos los datos temporalmente en la sesión para validarlos después
            session([
                'two_factor_code' => $codigo2FA,
                'two_factor_expires_at' => Carbon::now()->addMinutes(15),
                'two_factor_email' => $request->input('email')
            ]);

            // 3. Enviamos el correo real utilizando el módulo que creaste
            Mail::to($request->input('email'))->send(new CodigoVerificacionMail($codigo2FA));

            // 4. Redirigimos al usuario a la vista para escribir el código
            return redirect()->route('auth.two-factor');

        } catch (AuthenticationException $e) {
            return back()->withErrors([
                'email' => $e->getMessage(),
            ])->onlyInput('email');
        }
    }

    public function verifyTwoFactor(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'code' => 'required|numeric',
        ]);

        if ($request->input('code') == session('two_factor_code') && 
            \Carbon\Carbon::now()->isBefore(session('two_factor_expires_at'))) {
            
            // Si el código es correcto y no ha expirado, limpiamos la sesión y marcamos como verificado
            session()->forget(['two_factor_code', 'two_factor_expires_at']);
            session(['two_factor_verified' => true]);
            
            return redirect()->intended(route('dashboard'));
        }

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
        try {
            $this->authService->logout(auth()->id());
        } catch (\Throwable) {
            // Silently handle token revocation errors
        }

        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route('login');
    }
}
