<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * ═══════════════════════════════════════════════════════
 * FORM REQUEST: Update Usuario (Actualización de Usuario)
 * ═══════════════════════════════════════════════════════
 * Usa "sometimes" en lugar de "required" para permitir actualizaciones
 * parciales. La validación de contraseña es condicional: solo se aplica
 * si el campo está presente. Excluye el email actual de la validación única.
 */
class UpdateUsuariosRequest extends FormRequest
{
    /**
     * ═══════════════════════════════════════════════
     * AUTORIZACIÓN
     * ───────────────────────────────────────────────
     * Solo usuarios autenticados pueden actualizar usuarios.
     * Verifica que exista una sesión activa mediante auth()->check().
     * ═══════════════════════════════════════════════
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * ═══════════════════════════════════════════════
     * REGLAS DE VALIDACIÓN
     * ───────────────────────────────────────────────
     * - usuario_nombre:  Opcional (sometimes), texto, máx. 60 caracteres
     * - email:           Opcional (sometimes), email, máx. 50, único (excluye actual), @usap.edu
     * - contrasena:      Opcional (sometimes), texto, máx. 50, min 8, mayúsculas, minúsculas, números
     * - rol_id:          Opcional (sometimes), debe existir en roles
     * - procurador_id:   Opcional (nullable), debe existir en procuradores
     * ═══════════════════════════════════════════════
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'usuario_nombre' => 'sometimes|string|max:60',                                                                                                      // ─── Opcional, nombre (máx. 60 caracteres)
            'email' => 'sometimes|email|max:50|unique:usuarios,email,'.$this->route('id').',usuario_id|ends_with:@usap.edu',                                    // ─── Opcional, email único @usap.edu (excluye actual)
            'contrasena' => ['sometimes', 'string', 'max:50', Password::min(8)->mixedCase()->numbers()],                                                        // ─── Opcional, contraseña segura (mín. 8, mayús, minús, números)
            'rol_id' => 'sometimes|exists:roles,rol_id',                                                                                                        // ─── Opcional, rol existente
            'procurador_id' => 'nullable|exists:procuradores,procurador_id',                                                                                    // ─── Opcional, procurador existente
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
