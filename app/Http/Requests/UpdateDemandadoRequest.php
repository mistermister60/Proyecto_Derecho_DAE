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
