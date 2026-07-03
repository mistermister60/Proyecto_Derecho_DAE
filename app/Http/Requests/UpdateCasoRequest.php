<?php

namespace App\Http\Requests;

use App\Enums\CasoEstadoEnum;
use App\Enums\RolEnum;
use App\Models\Caso;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCasoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $caso = Caso::where('caso_numero_expediente', $this->route('expediente'))->firstOrFail();

        return $this->user()->can('update', $caso);
    }

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
            // Sustitucion de string magico por validacion estricta basada en Enum
            'caso_estado' => 'required|in:'.implode(',', CasoEstadoEnum::values()),
        ]);
    }

    public function esDirector(): bool
    {
        return RolEnum::equals($this->user()?->rol?->rol_nombre, RolEnum::DIRECTOR);
    }
}
