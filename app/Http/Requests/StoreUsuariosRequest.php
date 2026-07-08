<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Form request para almacenar un nuevo usuario.
 *
 * Valida los datos del usuario incluyendo la regla Password de Laravel
 * (mínimo 8 caracteres, mayúsculas, minúsculas y números).
 * La contraseña se encripta en el controlador.
 */
class StoreUsuariosRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'usuario_nombre' => 'required|string|max:60',
            'email' => 'required|email|max:50|unique:usuarios,email',
            'contrasena' => ['required', 'string', 'max:50', Password::min(8)->mixedCase()->numbers()],
            'rol_id' => 'required|exists:roles,rol_id',
            'procurador_id' => 'nullable|exists:procuradores,procurador_id',
        ];
    }
}
