<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ═══════════════════════════════════════════════════════
 * FORM REQUEST: Update Demandado (Actualización de Demandado)
 * ═══════════════════════════════════════════════════════
 * Valida los campos editables del demandado. Excluye el DNI actual
 * de la validación única para permitir mantener el mismo valor.
 */
class UpdateDemandadoRequest extends FormRequest
{
    /**
     * ═══════════════════════════════════════════════
     * AUTORIZACIÓN
     * ───────────────────────────────────────────────
     * Solo usuarios autenticados pueden actualizar demandados.
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
     * - demandado_nombre:           Obligatorio, texto, máx. 100 caracteres
     * - demandado_apellido:         Obligatorio, texto, máx. 100 caracteres
     * - demandado_dni:              Obligatorio, texto, máx. 20, único (excluye actual)
     * - demandado_telefono:         Opcional, texto, máx. 20 caracteres
     * - demandado_direccion:        Obligatorio, texto libre
     * - demandado_profesion:        Opcional, texto, máx. 100 caracteres
     * - demandado_lugar_trabajo:    Opcional, texto, máx. 100 caracteres
     * - demandado_direccion_trabajo: Opcional, texto, máx. 350 caracteres
     * - demandado_telefono_trabajo: Opcional, texto, máx. 29 caracteres
     * ═══════════════════════════════════════════════
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'demandado_nombre' => 'required|string|max:100',                                                                          // ─── Obligatorio, nombre (máx. 100 caracteres)
            'demandado_apellido' => 'required|string|max:100',                                                                        // ─── Obligatorio, apellido (máx. 100 caracteres)
            'demandado_dni' => 'required|string|max:20|unique:demandados,demandado_dni,'.$this->route('identidad').',demandado_dni',  // ─── Obligatorio, DNI único (excluye el actual)
            'demandado_telefono' => 'nullable|string|max:20',                                                                         // ─── Opcional, teléfono (máx. 20 caracteres)
            'demandado_direccion' => 'required|string',                                                                               // ─── Obligatorio, dirección (texto libre)
            'demandado_profesion' => 'nullable|string|max:100',                                                                       // ─── Opcional, profesión (máx. 100 caracteres)
            'demandado_lugar_trabajo' => 'nullable|string|max:100',                                                                   // ─── Opcional, lugar de trabajo (máx. 100 caracteres)
            'demandado_direccion_trabajo' => 'nullable|string|max:350',                                                               // ─── Opcional, dirección trabajo (máx. 350 caracteres)
            'demandado_telefono_trabajo' => 'nullable|string|max:29',                                                                 // ─── Opcional, teléfono trabajo (máx. 29 caracteres)
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
            'demandado_nombre.required' => 'El nombre del demandado es obligatorio.',
            'demandado_nombre.max' => 'El nombre no puede tener más de :max caracteres.',
            'demandado_apellido.required' => 'El apellido del demandado es obligatorio.',
            'demandado_apellido.max' => 'El apellido no puede tener más de :max caracteres.',
            'demandado_dni.required' => 'El número de DNI del demandado es obligatorio.',
            'demandado_dni.max' => 'El DNI no puede tener más de :max caracteres.',
            'demandado_dni.unique' => 'Este número de DNI ya está registrado en el sistema.',
            'demandado_telefono.max' => 'El teléfono no puede tener más de :max caracteres.',
            'demandado_direccion.required' => 'La dirección del demandado es obligatoria.',
            'demandado_profesion.max' => 'La profesión no puede tener más de :max caracteres.',
            'demandado_lugar_trabajo.max' => 'El lugar de trabajo no puede tener más de :max caracteres.',
            'demandado_direccion_trabajo.max' => 'La dirección de trabajo no puede tener más de :max caracteres.',
            'demandado_telefono_trabajo.max' => 'El teléfono del trabajo no puede tener más de :max caracteres.',
        ];
    }
}
