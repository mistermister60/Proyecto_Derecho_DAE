<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Usuario>
 */
class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'rol_id' => 1,
            'procurador_id' => null,
            'usuario_nombre' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'contrasena' => static::$password ??= Hash::make('password'),
            'usuario_estado' => 'activo',
            'remember_token' => Str::random(10),
        ];
    }

    public function director(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol_id' => 1,
        ]);
    }

    public function procurador(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol_id' => 2,
        ]);
    }
}
