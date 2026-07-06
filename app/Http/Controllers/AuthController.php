<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthenticationException;
use App\Http\Requests\LoginCredentialsRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
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
            $authResponse = $this->authService->attemptLogin(
                $request->input('email'),
                $request->input('contrasena')
            );

            return redirect()->intended(route('dashboard'));

        } catch (AuthenticationException $e) {
            return back()->withErrors([
                'email' => $e->getMessage(),
            ])->onlyInput('email');
        }
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
