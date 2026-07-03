<?php

namespace App\Http\DTOs;

class AuthResponse
{
    public string $token;

    public string $tokenType = 'Bearer';

    public int $expiresIn;

    public array $user;

    public array $permissions;

    public array $roles;

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
     * Convert to array for JSON response
     *
     * @return array<string, mixed>
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
