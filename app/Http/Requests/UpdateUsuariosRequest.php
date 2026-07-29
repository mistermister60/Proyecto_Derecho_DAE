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
            'email' => 'sometimes|email|max:50|unique:usuarios,email,'.$this->route('id').',usuario_id|ends_with:@usap.edu',
            'contrasena' => ['sometimes', 'string', 'max:50', Password::min(8)->mixedCase()->numbers()],
            'rol_id' => 'sometimes|exists:roles,rol_id',
            'procurador_id' => 'nullable|exists:procuradores,procurador_id',
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'usuario_nombre.max' => 'El nombre no puede tener más de :max caracteres.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.max' => 'El correo electrónico no puede tener más de :max caracteres.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'email.ends_with' => 'El correo debe terminar en @usap.edu.',
            'contrasena.max' => 'La contraseña no puede tener más de :max caracteres.',
            'contrasena.min' => 'La contraseña debe tener al menos :min caracteres.',
            'contrasena.mixed' => 'La contraseña debe contener mayúsculas y minúsculas.',
            'contrasena.numbers' => 'La contraseña debe contener al menos un número.',
            'rol_id.exists' => 'El rol seleccionado no existe.',
            'procurador_id.exists' => 'El procurador seleccionado no existe.',
        ];
    }
}
