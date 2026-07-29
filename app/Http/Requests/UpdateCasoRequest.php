<?php

namespace App\Http\Requests;

use App\Enums\CasoEstadoEnum;
use App\Enums\RolEnum;
use App\Models\Caso;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request para actualizar un caso legal existente.
 *
 * Incluye lógica de autorización basada en policies de Laravel y validación
 * condicional: los usuarios con rol de Director tienen reglas adicionales
 * que el resto de los usuarios no pueden modificar.
 */
class UpdateCasoRequest extends FormRequest
{
    /**
     * Instancia del caso que se está actualizando, resuelta durante la autorización.
     */
    public ?Caso $caso = null;

    /**
     * Determine if the user is authorized to make this request.
     *
     * Resuelve el caso a partir del parámetro de ruta 'expediente' y
     * delega la autorización a la CasoPolicy.
     *
     *
     * @throws ModelNotFoundException
     */
    public function authorize(): bool
    {
        $this->caso = Caso::where('caso_numero_expediente', $this->route('expediente'))->firstOrFail();

        return $this->user()->can('update', $this->caso);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Las reglas base aplican a todos los usuarios. Si el usuario autenticado
     * tiene rol de Director, se agregan reglas adicionales (cliente, demandado,
     * tipo_trámite, procurador, fecha, observaciones, admisibilidad y estado).
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        $comunes = [
            'estado_id' => 'required|exists:estados_caso,estado_id',
            'caso_parte_representada' => 'required|string|max:50',
            'caso_juzgado' => 'nullable|string|max:50',
            'caso_relacion_hechos' => 'required|string',
        ];

        if (! $this->esDirector()) {
            return $comunes;
        }

        return array_merge($comunes, [
            'cliente_id' => 'required|exists:clientes,cliente_id',
            'demandado_id' => 'nullable|exists:demandados,demandado_id',
            'tipo_tramite_id' => 'required|exists:tipos_tramite,tipo_tramite_id',
            'procurador_id' => 'required|exists:procuradores,procurador_id',
            'caso_fecha_interpuesta' => 'nullable|date',
            'caso_observaciones_director' => 'nullable|string',
            'caso_admisible' => 'boolean',
            // Sustitución de string mágico por validación estricta basada en Enum
            'caso_estado' => 'required|in:'.implode(',', CasoEstadoEnum::values()),
        ]);
    }

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
