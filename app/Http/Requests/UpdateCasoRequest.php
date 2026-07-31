<?php

namespace App\Http\Requests;

use App\Enums\CasoEstadoEnum;
use App\Enums\RolEnum;
use App\Models\Caso;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;

/**
 * ═══════════════════════════════════════════════════════
 * FORM REQUEST: Update Caso (Actualización de Caso Legal)
 * ═══════════════════════════════════════════════════════
 * Valida la actualización de un caso legal existente.
 * Incluye autorización basada en policies de Laravel y validación condicional:
 * los usuarios con rol de Director tienen reglas adicionales que el resto
 * de usuarios no pueden modificar.
 */
class UpdateCasoRequest extends FormRequest
{
    /**
     * Instancia del caso que se está actualizando, resuelta durante la autorización.
     */
    public ?Caso $caso = null;

    /**
     * ═══════════════════════════════════════════════
     * AUTORIZACIÓN
     * ───────────────────────────────────────────────
     * Resuelve el caso a partir del parámetro de ruta 'expediente' y
     * delega la autorización a la CasoPolicy.
     * ═══════════════════════════════════════════════
     *
     * @throws ModelNotFoundException
     */
    public function authorize(): bool
    {
        $this->caso = Caso::where('caso_numero_expediente', $this->route('expediente'))->firstOrFail();

        return $this->user()->can('update', $this->caso);
    }

    /**
     * ═══════════════════════════════════════════════
     * REGLAS DE VALIDACIÓN
     * ───────────────────────────────────────────────
     * Reglas base (todos los usuarios):
     * - estado_id:                Obligatorio, debe existir en estados_caso
     * - caso_parte_representada:  Obligatorio, texto, máx. 50 caracteres
     * - caso_juzgado:             Opcional, texto, máx. 50 caracteres
     * - caso_relacion_hechos:     Obligatorio, texto libre
     *
     * Reglas adicionales SOLO para Director:
     * - cliente_id:               Obligatorio, debe existir en clientes
     * - demandado_id:             Opcional, debe existir en demandados
     * - tipo_tramite_id:          Obligatorio, debe existir en tipos_tramite
     * - procurador_id:            Obligatorio, debe existir en procuradores
     * - caso_fecha_interpuesta:   Opcional, fecha válida
     * - caso_observaciones_director: Opcional, texto libre
     * - caso_admisible:           Booleano
     * - caso_estado:              Obligatorio, valores del Enum CasoEstadoEnum
     * ═══════════════════════════════════════════════
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        $comunes = [
            'estado_id' => 'required|exists:estados_caso,estado_id',                                          // ─── Obligatorio, estado del caso existente
            'caso_parte_representada' => 'required|string|max:50',                                            // ─── Obligatorio, parte representada (máx. 50 caracteres)
            'caso_juzgado' => 'nullable|string|max:50',                                                       // ─── Opcional, juzgado (máx. 50 caracteres)
            'caso_relacion_hechos' => 'required|string',                                                      // ─── Obligatorio, relato de los hechos
        ];

        if (! $this->esDirector()) {
            return $comunes;
        }

        return array_merge($comunes, [
            'cliente_id' => 'required|exists:clientes,cliente_id',                                            // ─── Obligatorio, cliente existente
            'demandado_id' => 'nullable|exists:demandados,demandado_id',                                      // ─── Opcional, demandado existente
            'tipo_tramite_id' => 'required|exists:tipos_tramite,tipo_tramite_id',                             // ─── Obligatorio, tipo de trámite existente
            'procurador_id' => 'required|exists:procuradores,procurador_id',                                  // ─── Obligatorio, procurador existente
            'caso_fecha_interpuesta' => 'nullable|date',                                                      // ─── Opcional, fecha válida
            'caso_observaciones_director' => 'nullable|string',                                               // ─── Opcional, observaciones del director
            'caso_admisible' => 'boolean',                                                                    // ─── Booleano: admisible o no
            // Sustitución de string mágico por validación estricta basada en Enum
            'caso_estado' => 'required|in:'.implode(',', CasoEstadoEnum::values()),                           // ─── Obligatorio, estado válido del Enum
        ]);
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
            'estado_id.required' => 'Debe seleccionar un estado del caso.',
            'estado_id.exists' => 'El estado del caso seleccionado no existe.',
            'caso_parte_representada.required' => 'La parte representada es obligatoria.',
            'caso_parte_representada.max' => 'La parte representada no puede tener más de :max caracteres.',
            'caso_juzgado.max' => 'El nombre del juzgado no puede tener más de :max caracteres.',
            'caso_relacion_hechos.required' => 'La relación de hechos es obligatoria.',
            // Director-only
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'cliente_id.exists' => 'El cliente seleccionado no existe.',
            'tipo_tramite_id.required' => 'Debe seleccionar un tipo de trámite.',
            'tipo_tramite_id.exists' => 'El tipo de trámite seleccionado no existe.',
            'procurador_id.required' => 'Debe seleccionar un procurador.',
            'procurador_id.exists' => 'El procurador seleccionado no existe.',
            'demandado_id.exists' => 'El demandado seleccionado no existe.',
            'caso_fecha_interpuesta.date' => 'La fecha interpuesta no tiene un formato válido.',
            'caso_admisible.boolean' => 'El valor de admisible debe ser verdadero o falso.',
            'caso_estado.required' => 'El estado del caso es obligatorio.',
            'caso_estado.in' => 'El estado del caso seleccionado no es válido.',
        ];
    }

    /**
     * Determine if the authenticated user has the Director role.
     */
    public function esDirector(): bool
    {
        return RolEnum::equals($this->user()?->rol?->rol_nombre, RolEnum::DIRECTOR);
    }
}
