<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ═══════════════════════════════════════════════════════
 * FORM REQUEST: Update Cliente (Actualización de Cliente)
 * ═══════════════════════════════════════════════════════
 * Valida los datos editables del cliente. Utiliza cliente_nombre y
 * cliente_apellido en lugar de nombre_completo. Excluye el DNI actual
 * de la validación única para permitir mantener el mismo valor.
 */
class UpdateClienteRequest extends FormRequest
{
    /**
     * ═══════════════════════════════════════════════
     * AUTORIZACIÓN
     * ───────────────────────────────────────────────
     * Solo usuarios autenticados pueden actualizar clientes.
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
     * - cliente_nombre:           Obligatorio, texto, máx. 100 caracteres
     * - cliente_apellido:         Obligatorio, texto, máx. 100 caracteres
     * - cliente_dni:              Obligatorio, texto, máx. 20, único (excluye actual)
     * - cliente_estado_civil:     Obligatorio, texto, máx. 30 caracteres
     * - cliente_telefono:         Obligatorio, texto, máx. 20 caracteres
     * - cliente_direccion:        Obligatorio, texto libre
     * - cliente_numero_hijos:     Opcional, entero >= 0
     * - cliente_nombres_hijos:    Opcional, texto, máx. 250 caracteres
     * - cliente_profesion:        Opcional, texto, máx. 100 caracteres
     * - cliente_lugar_trabajo:    Opcional, texto, máx. 100 caracteres
     * - cliente_direccion_trabajo: Opcional, texto, máx. 350 caracteres
     * - cliente_telefono_trabajo: Opcional, texto, máx. 29 caracteres
     * - cliente_salario_mensual:  Opcional, numérico >= 0
     * ═══════════════════════════════════════════════
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'cliente_nombre' => 'required|string|max:100',                                                                          // ─── Obligatorio, nombre (máx. 100 caracteres)
            'cliente_apellido' => 'required|string|max:100',                                                                        // ─── Obligatorio, apellido (máx. 100 caracteres)
            'cliente_dni' => 'required|string|max:20|unique:clientes,cliente_dni,'.$this->route('identidad').',cliente_dni',        // ─── Obligatorio, DNI único (excluye el actual)
            'cliente_estado_civil' => 'required|string|max:30',                                                                     // ─── Obligatorio, estado civil (máx. 30 caracteres)
            'cliente_telefono' => 'required|string|max:20',                                                                         // ─── Obligatorio, teléfono (máx. 20 caracteres)
            'cliente_direccion' => 'required|string',                                                                               // ─── Obligatorio, dirección (texto libre)
            'cliente_numero_hijos' => 'nullable|integer|min:0',                                                                     // ─── Opcional, número de hijos (entero no negativo)
            'cliente_nombres_hijos' => 'nullable|string|max:250',                                                                   // ─── Opcional, nombres de hijos (máx. 250 caracteres)
            'cliente_profesion' => 'nullable|string|max:100',                                                                       // ─── Opcional, profesión (máx. 100 caracteres)
            'cliente_lugar_trabajo' => 'nullable|string|max:100',                                                                   // ─── Opcional, lugar de trabajo (máx. 100 caracteres)
            'cliente_direccion_trabajo' => 'nullable|string|max:350',                                                               // ─── Opcional, dirección trabajo (máx. 350 caracteres)
            'cliente_telefono_trabajo' => 'nullable|string|max:29',                                                                 // ─── Opcional, teléfono trabajo (máx. 29 caracteres)
            'cliente_salario_mensual' => 'nullable|numeric|min:0',                                                                  // ─── Opcional, salario mensual (numérico no negativo)
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
            'cliente_nombre.required' => 'El nombre del cliente es obligatorio.',
            'cliente_nombre.max' => 'El nombre no puede tener más de :max caracteres.',
            'cliente_apellido.required' => 'El apellido del cliente es obligatorio.',
            'cliente_apellido.max' => 'El apellido no puede tener más de :max caracteres.',
            'cliente_dni.required' => 'El número de DNI del cliente es obligatorio.',
            'cliente_dni.max' => 'El DNI no puede tener más de :max caracteres.',
            'cliente_dni.unique' => 'Este número de DNI ya está registrado en el sistema.',
            'cliente_estado_civil.required' => 'El estado civil del cliente es obligatorio.',
            'cliente_estado_civil.max' => 'El estado civil no puede tener más de :max caracteres.',
            'cliente_telefono.required' => 'El teléfono del cliente es obligatorio.',
            'cliente_telefono.max' => 'El teléfono no puede tener más de :max caracteres.',
            'cliente_direccion.required' => 'La dirección del cliente es obligatoria.',
            'cliente_numero_hijos.integer' => 'El número de hijos debe ser un valor entero.',
            'cliente_numero_hijos.min' => 'El número de hijos no puede ser negativo.',
            'cliente_nombres_hijos.max' => 'Los nombres de los hijos no pueden exceder :max caracteres.',
            'cliente_profesion.max' => 'La profesión no puede tener más de :max caracteres.',
            'cliente_lugar_trabajo.max' => 'El lugar de trabajo no puede tener más de :max caracteres.',
            'cliente_direccion_trabajo.max' => 'La dirección de trabajo no puede tener más de :max caracteres.',
            'cliente_telefono_trabajo.max' => 'El teléfono del trabajo no puede tener más de :max caracteres.',
            'cliente_salario_mensual.numeric' => 'El salario mensual debe ser un valor numérico.',
            'cliente_salario_mensual.min' => 'El salario mensual no puede ser negativo.',
        ];
    }
}
