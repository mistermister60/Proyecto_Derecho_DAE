<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ═══════════════════════════════════════════════════════
 * FORM REQUEST: Store Caso (Creación de Caso Legal)
 * ═══════════════════════════════════════════════════════
 * Valida los datos necesarios para crear un nuevo caso legal.
 * Incluye las relaciones con cliente, tipo de trámite y procurador,
 * así como los detalles descriptivos y observaciones del caso.
 */
class StoreCasoRequest extends FormRequest
{
    /**
     * ═══════════════════════════════════════════════
     * AUTORIZACIÓN
     * ───────────────────────────────────────────────
     * Cualquier usuario autenticado puede crear un caso.
     * El middleware 'auth' ya garantiza sesión activa.
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
     * - cliente_id:           Obligatorio, debe existir en clientes
     * - tipo_tramite_id:      Obligatorio, debe existir en tipos_trámite
     * - procurador_id:        Obligatorio, debe existir en procuradores
     * - caso_parte_representada: Obligatorio, texto, máx. 50 caracteres
     * - caso_juzgado:         Opcional, texto, máx. 50 caracteres
     * - caso_relacion_hechos: Obligatorio, texto largo
     * - caso_observaciones_director: Opcional, texto largo
     * ═══════════════════════════════════════════════
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'cliente_id' => 'required|exists:clientes,cliente_id',                                          // ─── Obligatorio, debe ser un cliente existente
            'tipo_tramite_id' => 'required|exists:tipos_tramite,tipo_tramite_id',                             // ─── Obligatorio, debe ser un tipo de trámite existente
            'procurador_id' => 'required|exists:procuradores,procurador_id',                                 // ─── Obligatorio, debe ser un procurador existente
            'caso_parte_representada' => 'required|string|max:50',                                           // ─── Obligatorio, texto hasta 50 caracteres
            'caso_juzgado' => 'nullable|string|max:50',                                                      // ─── Opcional, texto hasta 50 caracteres
            'caso_relacion_hechos' => 'required|string',                                                     // ─── Obligatorio, texto libre (relato de los hechos)
            'caso_observaciones_director' => 'nullable|string',                                              // ─── Opcional, texto libre (notas internas del director)
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
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'cliente_id.exists' => 'El cliente seleccionado no existe en el sistema.',
            'tipo_tramite_id.required' => 'Debe seleccionar un tipo de trámite.',
            'tipo_tramite_id.exists' => 'El tipo de trámite seleccionado no existe.',
            'procurador_id.required' => 'Debe seleccionar un procurador.',
            'procurador_id.exists' => 'El procurador seleccionado no existe.',
            'caso_parte_representada.required' => 'La parte representada es obligatoria.',
            'caso_parte_representada.max' => 'La parte representada no puede tener más de :max caracteres.',
            'caso_juzgado.max' => 'El nombre del juzgado no puede tener más de :max caracteres.',
            'caso_relacion_hechos.required' => 'La relación de hechos es obligatoria.',
        ];
    }
}
