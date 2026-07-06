<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request para actualizar un procurador existente.
 *
 * Valida los campos editables del procurador. Excluye el registro actual
 * de las validaciones únicas (DNI, correo, colegiación) para permitir
 * mantener los mismos valores.
 */
class UpdateProcuradorRequest extends FormRequest
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
        $identidad = $this->route('identidad');

        return [
            'procurador_nombre' => 'required|string|max:30',
            'procurador_apellido' => 'required|string|max:30',
            'procurador_dni' => 'required|string|max:20|unique:procuradores,procurador_dni,'.$identidad.',procurador_dni',
            'procurador_telefono' => 'required|string|max:15',
            'procurador_direccion' => 'required|string|max:350',
            'procurador_correo' => 'required|email|max:50|unique:procuradores,procurador_correo,'.$identidad.',procurador_correo',
            'procurador_profesion' => 'required|string|max:50',
            'procurador_colegiacion' => 'required|string|max:50|unique:procuradores,procurador_colegiacion,'.$identidad.',procurador_colegiacion',
            'procurador_fecha_nacimiento' => 'required|date',
        ];
    }
}
