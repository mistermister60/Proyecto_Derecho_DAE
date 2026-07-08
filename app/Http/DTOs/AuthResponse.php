<?php

namespace App\Http\DTOs;

/**
 * AuthResponse — DTO para la respuesta de autenticación.
 *
 * Transporta los datos que se devuelven al cliente tras un inicio
 * de sesión exitoso: token JWT, tipo de token, tiempo de expiración,
 * datos del usuario autenticado y sus permisos/roles asociados.
 */
class AuthResponse
{
    /**
     * Token JWT de acceso.
     */
    public string $token;

    /**
     * Tipo de token (por defecto "Bearer").
     */
    public string $tokenType = 'Bearer';

    /**
     * Tiempo de expiración del token en segundos.
     */
    public int $expiresIn;

    /**
     * Datos del usuario autenticado.
     *
     * @var array<string, mixed>
     */
    public array $user;

    /**
     * Lista de permisos asignados al usuario.
     *
     * @var array<int, string>
     */
    public array $permissions;

    /**
     * Lista de roles asignados al usuario.
     *
     * @var array<int, string>
     */
    public array $roles;

    /**
     * @param  string  $token  Token JWT de acceso.
     * @param  int  $expiresIn  Tiempo de expiración en segundos.
     * @param  array<string, mixed>  $user  Datos del usuario autenticado.
     * @param  array<int, string>  $permissions  Lista de permisos (opcional).
     * @param  array<int, string>  $roles  Lista de roles (opcional).
     */
    public function __construct(
        string $token,
        int $expiresIn,
        array $user,
        array $permissions = [],
        array $roles = []
    ) {
        $this->token = $token;
        $this->expiresIn = $expiresIn;
        $this->user = $user;
        $this->permissions = $permissions;
        $this->roles = $roles;
    }

    /**
     * Convertir el DTO a un array asociativo para la respuesta JSON.
     *
     * @return array<string, mixed> Array con token, token_type, expires_in,
     *                              user, permissions y roles.
     */
    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'token_type' => $this->tokenType,
            'expires_in' => $this->expiresIn,
            'user' => $this->user,
            'permissions' => $this->permissions,
            'roles' => $this->roles,
        ];
    }
}
