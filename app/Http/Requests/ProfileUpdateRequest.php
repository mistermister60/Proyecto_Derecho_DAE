<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * ═══════════════════════════════════════════════════════
 * FORM REQUEST: Profile Update (Actualización de Perfil)
 * ═══════════════════════════════════════════════════════
 * Valida los datos enviados al actualizar el perfil del usuario autenticado.
 * Asegura que el email sea único en la tabla de usuarios,
 * excluyendo al usuario actual de la verificación de unicidad.
 */
class ProfileUpdateRequest extends FormRequest
{
    /**
     * ═══════════════════════════════════════════════
     * AUTORIZACIÓN
     * ───────────────────────────────────────────────
     * Cualquier usuario autenticado puede actualizar su propio perfil.
     * El middleware 'auth' ya garantiza que hay sesión activa.
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
     * - usuario_nombre: Obligatorio, texto, máx. 255 caracteres
     * - email:          Obligatorio, texto, minúsculas, formato email,
     *                   máx. 255 caracteres, único en usuarios
     *                   (ignora el ID del usuario actual)
     * ═══════════════════════════════════════════════
     *
     * @return array<string, array<int, Rule|string>>
     */
    public function rules(): array
    {
        return [
            'usuario_nombre' => ['required', 'string', 'max:255'],       // ─── Obligatorio, texto hasta 255 caracteres
            'email' => [
                'required',                                               // ─── Campo obligatorio
                'string',                                                 // ─── Debe ser texto
                'lowercase',                                              // ─── Se convierte/valida a minúsculas
                'email',                                                  // ─── Formato email válido
                'max:255',                                                // ─── Máximo 255 caracteres
                Rule::unique(Usuario::class)->ignore($this->user()->usuario_id, 'usuario_id'), // ─── Único, excepto el propio usuario
            ],
        ];
    }

    // ─── Mensajes personalizados de error ───────────────────────────────
    // Traduce los errores de validación a español para el usuario final.
    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'usuario_nombre.required' => 'El nombre de usuario es obligatorio.',
            'usuario_nombre.max' => 'El nombre no puede tener más de :max caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.max' => 'El correo electrónico no puede tener más de :max caracteres.',
            'email.unique' => 'Este correo electrónico ya está en uso.',
            'email.lowercase' => 'El correo electrónico debe estar en minúsculas.',
        ];
    }
}
