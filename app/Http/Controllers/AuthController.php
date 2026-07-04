<?php

namespace App\Http\Controllers;

use App\Exceptions\AccountInactiveException;
use App\Exceptions\AuthenticationException;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\RateLimitExceededException;
use App\Http\Requests\LoginCredentialsRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

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

    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout(auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Cierre de sesión exitoso',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión',
            ], 500);
        }
    }

    private function getErrorCode(AuthenticationException $exception): string
    {
        return match (get_class($exception)) {
            InvalidCredentialsException::class => 'INVALID_CREDENTIALS',
            AccountInactiveException::class => 'ACCOUNT_INACTIVE',
            RateLimitExceededException::class => 'RATE_LIMIT_EXCEEDED',
            default => 'AUTHENTICATION_ERROR',
        };
    }

    private function getHttpStatusCode(AuthenticationException $exception): int
    {
        return match (get_class($exception)) {
            RateLimitExceededException::class => 429,
            AccountInactiveException::class => 403,
            default => 401,
        };
    }
}
