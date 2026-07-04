<?php

namespace App\Services;

use App\Exceptions\AccountInactiveException;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\RateLimitExceededException;
use App\Http\DTOs\AuthResponse;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    private const RATE_LIMIT_KEY_PREFIX = 'login_attempts:';

    private const RATE_LIMIT_MAX_ATTEMPTS = 5;

    private const RATE_LIMIT_EXPIRY = 300; // 5 minutes

    private const TOKEN_EXPIRY = 3600; // 1 hour

    public function attemptLogin(string $email, string $password): AuthResponse
    {
        $this->checkRateLimit($email);

        $usuario = $this->findUserByEmail($email);
        $this->validateCredentials($usuario, $password, $email);
        $this->validateAccountStatus($usuario);

        Auth::login($usuario);

        $token = $this->generateToken($usuario);
        $this->recordSuccessfulLogin($email);

        return $this->createAuthResponse($usuario, $token);
    }

    public function logout(string $userId): void
    {
        $usuario = Usuario::findOrFail($userId);
        Auth::logout();
        $usuario->tokens()->delete();
    }

    private function checkRateLimit(string $email): void
    {
        $key = $this->getRateLimitKey($email);
        $attempts = Cache::get($key, 0);

        if ($attempts >= self::RATE_LIMIT_MAX_ATTEMPTS) {
            throw new RateLimitExceededException;
        }
    }

    private function findUserByEmail(string $email): ?Usuario
    {
        return Usuario::where('email', $email)->first();
    }

    private function validateCredentials(?Usuario $usuario, string $password, string $email): void
    {
        if (! $usuario || ! $this->isValidPassword($usuario->contrasena, $password)) {
            $this->recordFailedLoginAttempt($email);
            throw new InvalidCredentialsException;
        }
    }

    private function isValidPassword(string $hashedPassword, string $plainPassword): bool
    {
        return Hash::check($plainPassword, $hashedPassword);
    }

    private function validateAccountStatus(Usuario $usuario): void
    {
        if ($usuario->usuario_estado !== 'activo') {
            throw new AccountInactiveException;
        }
    }

    private function generateToken(Usuario $usuario): string
    {
        $token = Str::random(60);
        $usuario->tokens()->create([
            'name' => 'auth_token',
            'token' => hash('sha256', $token),
            'abilities' => ['*'],
        ]);

        return $token;
    }

    private function recordSuccessfulLogin(string $email): void
    {
        $this->clearRateLimit($email);
    }

    private function recordFailedLoginAttempt(string $email): void
    {
        $key = $this->getRateLimitKey($email);
        $attempts = Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, self::RATE_LIMIT_EXPIRY);
    }

    private function clearRateLimit(string $email): void
    {
        $key = $this->getRateLimitKey($email);
        Cache::forget($key);
    }

    private function getRateLimitKey(string $email): string
    {
        return self::RATE_LIMIT_KEY_PREFIX.$email;
    }

    private function createAuthResponse(Usuario $usuario, string $token): AuthResponse
    {
        return new AuthResponse(
            token: $token,
            expiresIn: self::TOKEN_EXPIRY,
            user: [
                'id' => $usuario->usuario_id,
                'nombre' => $usuario->usuario_nombre,
                'email' => $usuario->email,
                'estado' => $usuario->usuario_estado,
            ],
            permissions: $this->getUserPermissions($usuario),
            roles: $this->getUserRoles($usuario),
        );
    }

    private function getUserPermissions(Usuario $usuario): array
    {
        return $usuario->rol ? $usuario->rol->permisos ?? [] : [];
    }

    private function getUserRoles(Usuario $usuario): array
    {
        return $usuario->rol ? [$usuario->rol->rol_nombre] : [];
    }
}
