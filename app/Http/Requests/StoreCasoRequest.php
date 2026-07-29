<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request para almacenar un nuevo caso legal.
 *
 * Valida los datos necesarios para crear un caso en el sistema,
 * incluyendo las relaciones con cliente, tipo de trámite y procurador,
 * así como los detalles descriptivos del caso.
 */
class StoreCasoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'cliente_id' => 'required|exists:clientes,cliente_id',
            'tipo_tramite_id' => 'required|exists:tipos_tramite,tipo_tramite_id',
            'procurador_id' => 'required|exists:procuradores,procurador_id',
            'caso_parte_representada' => 'required|string|max:50',
            'caso_juzgado' => 'nullable|string|max:50',
            'caso_relacion_hechos' => 'required|string',
            'caso_observaciones_director' => 'nullable|string',
        ];
    }

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
