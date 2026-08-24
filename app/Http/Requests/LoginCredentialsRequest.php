<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ═══════════════════════════════════════════════════════
 * FORM REQUEST: Login Credentials (Credenciales de Inicio de Sesión)
 * ═══════════════════════════════════════════════════════
 * Valida el email y la contraseña enviados en el formulario de login
 * antes de que el controlador intente autenticar al usuario.
 * Garantiza formato de email válido y longitud mínima de contraseña.
 */
class LoginCredentialsRequest extends FormRequest
{
    /**
     * ═══════════════════════════════════════════════
     * AUTORIZACIÓN
     * ───────────────────────────────────────────────
     * Cualquier persona (sin autenticar) puede intentar iniciar sesión.
     * Siempre retorna true porque el gate real está en el controlador.
     * ═══════════════════════════════════════════════
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * ═══════════════════════════════════════════════
     * REGLAS DE VALIDACIÓN
     * ───────────────────────────────────────────────
     * - email:       Obligatorio, texto, formato email, máx. 255 caracteres
     * - contrasena:  Obligatorio, texto, mínimo 8 caracteres
     * ═══════════════════════════════════════════════
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'ends_with:@usap.edu'],       // ─── Obligatorio, email institucional @usap.edu válido, hasta 255 caracteres
            'contrasena' => ['required', 'string', 'min:8'],              // ─── Obligatorio, mínimo 8 caracteres por seguridad
        ];
    }

    // ─── Mensajes personalizados de error ───────────────────────────────
    // Traduce los errores de validación a español para el usuario final.
    /**
     * Get the custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.ends_with' => 'El correo debe terminar en @usap.edu.',
            'contrasena.required' => 'La contraseña es obligatoria.',
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
