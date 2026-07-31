<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * ═══════════════════════════════════════════════════════
 * FORM REQUEST: Store Usuario (Creación de Usuario)
 * ═══════════════════════════════════════════════════════
 * Valida los datos para crear un nuevo usuario del sistema.
 * Incluye validación de contraseña segura (mín. 8 chars, mayúsculas, minúsculas, números).
 * El email debe ser institucional (@usap.edu). La contraseña se encripta en el controlador.
 */
class StoreUsuariosRequest extends FormRequest
{
    /**
     * ═══════════════════════════════════════════════
     * AUTORIZACIÓN
     * ───────────────────────────────────────────────
     * Solo usuarios autenticados pueden crear nuevos usuarios.
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
     * - usuario_nombre: Obligatorio, texto, máx. 60 caracteres
     * - email:          Obligatorio, email, máx. 50, único en usuarios, termina en @usap.edu
     * - contrasena:     Opcional (a veces se genera automáticamente), string, máx. 50,
     *                   mínimo 8 caracteres, mayúsculas, minúsculas y números
     * - rol_id:         Obligatorio, debe existir en tabla roles
     * - procurador_id:  Opcional, debe existir en tabla procuradores.
     *                   OBLIGATORIO cuando rol_id = 2 (Procurador)
     * ═══════════════════════════════════════════════
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'usuario_nombre' => 'required|string|max:60',                                             // ─── Obligatorio, nombre de usuario (máx. 60 caracteres)
            'email' => 'required|email|max:50|unique:usuarios,email|ends_with:@usap.edu',             // ─── Obligatorio, email institucional único
            'contrasena' => ['sometimes', 'string', 'max:50', Password::min(8)->mixedCase()->numbers()], // ─── Opcional, contraseña segura (8+ chars, mayúsculas, minúsculas, números)
            'rol_id' => 'required|exists:roles,rol_id',                                               // ─── Obligatorio, rol existente
            'procurador_id' => ['nullable', 'exists:procuradores,procurador_id', 'required_if:rol_id,2'], // ─── Opcional salvo rol Procurador
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
            'usuario_nombre.required' => 'El nombre del usuario es obligatorio.',
            'usuario_nombre.max' => 'El nombre no puede tener más de :max caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.max' => 'El correo electrónico no puede tener más de :max caracteres.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'email.ends_with' => 'El correo debe terminar en @usap.edu.',
            'contrasena.max' => 'La contraseña no puede tener más de :max caracteres.',
            'contrasena.min' => 'La contraseña debe tener al menos :min caracteres.',
            'contrasena.mixed' => 'La contraseña debe contener mayúsculas y minúsculas.',
            'contrasena.numbers' => 'La contraseña debe contener al menos un número.',
            'rol_id.required' => 'Debe seleccionar un rol.',
            'rol_id.exists' => 'El rol seleccionado no existe.',
            'procurador_id.exists' => 'El procurador seleccionado no existe.',
            'procurador_id.required_if' => 'Debe seleccionar un procurador cuando el rol es Procurador.',
        ];
    }
}
