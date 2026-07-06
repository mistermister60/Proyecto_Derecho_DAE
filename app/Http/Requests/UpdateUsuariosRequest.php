<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Form request para actualizar un usuario existente.
 *
 * Usa "sometimes" en lugar de "required" para permitir actualizaciones
 * parciales. La validación de contraseña es condicional: solo se aplica
 * si el campo está presente. Excluye el email actual de la validación única.
 */
class UpdateUsuariosRequest extends FormRequest
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
            'usuario_nombre' => 'sometimes|string|max:60',
            'email' => 'sometimes|email|max:50|unique:usuarios,email,'.$this->route('id').',usuario_id',
            'contrasena' => ['sometimes', 'string', 'max:50', Password::min(8)->mixedCase()->numbers()],
            'rol_id' => 'sometimes|exists:roles,rol_id',
            'procurador_id' => 'nullable|exists:procuradores,procurador_id',
        ];
    }
}
