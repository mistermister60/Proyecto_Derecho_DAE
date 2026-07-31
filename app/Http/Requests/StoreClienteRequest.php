<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ═══════════════════════════════════════════════════════
 * FORM REQUEST: Store Cliente (Creación de Cliente)
 * ═══════════════════════════════════════════════════════
 * Valida los datos personales, laborales y familiares del cliente.
 * El campo nombre_completo se divide automáticamente en nombre y apellido
 * en el controlador. El DNI debe ser único en el sistema.
 */
class StoreClienteRequest extends FormRequest
{
    /**
     * ═══════════════════════════════════════════════
     * AUTORIZACIÓN
     * ───────────────────────────────────────────────
     * Solo usuarios autenticados pueden registrar clientes.
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
     * - nombre_completo:           Obligatorio, texto, máx. 200 caracteres
     * - cliente_dni:               Obligatorio, texto, máx. 20, único en clientes
     * - cliente_estado_civil:      Obligatorio, texto, máx. 30 caracteres
     * - cliente_telefono:          Obligatorio, texto, máx. 20 caracteres
     * - cliente_direccion:         Obligatorio, texto libre
     * - cliente_numero_hijos:      Opcional, entero >= 0
     * - cliente_nombres_hijos:     Opcional, texto, máx. 250 caracteres
     * - cliente_profesion:         Opcional, texto, máx. 100 caracteres
     * - cliente_lugar_trabajo:     Opcional, texto, máx. 100 caracteres
     * - cliente_direccion_trabajo: Opcional, texto, máx. 350 caracteres
     * - cliente_telefono_trabajo:  Opcional, texto, máx. 29 caracteres
     * - cliente_salario_mensual:   Opcional, numérico >= 0
     * ═══════════════════════════════════════════════
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'nombre_completo' => 'required|string|max:200',                               // ─── Obligatorio, nombre y apellido completos hasta 200 caracteres
            'cliente_dni' => 'required|string|max:20|unique:clientes,cliente_dni',         // ─── Obligatorio, DNI único (máx. 20 caracteres)
            'cliente_estado_civil' => 'required|string|max:30',                            // ─── Obligatorio, estado civil (soltero, casado, etc.)
            'cliente_telefono' => 'required|string|max:20',                                // ─── Obligatorio, teléfono de contacto (máx. 20 caracteres)
            'cliente_direccion' => 'required|string',                                      // ─── Obligatorio, dirección de residencia (texto libre)
            'cliente_numero_hijos' => 'nullable|integer|min:0',                            // ─── Opcional, número de hijos (entero no negativo)
            'cliente_nombres_hijos' => 'nullable|string|max:250',                          // ─── Opcional, nombres de los hijos (hasta 250 caracteres)
            'cliente_profesion' => 'nullable|string|max:100',                              // ─── Opcional, profesión u oficio (máx. 100 caracteres)
            'cliente_lugar_trabajo' => 'nullable|string|max:100',                          // ─── Opcional, nombre del empleador (máx. 100 caracteres)
            'cliente_direccion_trabajo' => 'nullable|string|max:350',                      // ─── Opcional, dirección laboral (máx. 350 caracteres)
            'cliente_telefono_trabajo' => 'nullable|string|max:29',                        // ─── Opcional, teléfono del trabajo (máx. 29 caracteres)
            'cliente_salario_mensual' => 'nullable|numeric|min:0',                         // ─── Opcional, salario mensual (valor numérico no negativo)
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
            'nombre_completo.required' => 'El nombre completo del cliente es obligatorio.',
            'nombre_completo.max' => 'El nombre completo no puede tener más de :max caracteres.',
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
