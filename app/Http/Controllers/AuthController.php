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

class AuthController extends BaseController
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function showLogin(): Response
    {
        return response()->view('auth.login');
    }

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
