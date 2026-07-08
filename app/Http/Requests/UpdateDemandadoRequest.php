<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request para actualizar un demandado existente.
 *
 * Valida los campos editables del demandado. Excluye el DNI actual
 * de la validación única para permitir mantener el mismo valor.
 */
class UpdateDemandadoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'demandado_nombre' => 'required|string|max:100',
            'demandado_apellido' => 'required|string|max:100',
            'demandado_dni' => 'required|string|max:20|unique:demandados,demandado_dni,'.$this->route('identidad').',demandado_dni',
            'demandado_telefono' => 'nullable|string|max:20',
            'demandado_direccion' => 'required|string',
            'demandado_profesion' => 'nullable|string|max:100',
            'demandado_lugar_trabajo' => 'nullable|string|max:100',
            'demandado_direccion_trabajo' => 'nullable|string|max:350',
            'demandado_telefono_trabajo' => 'nullable|string|max:29',
        ];
    }
}
