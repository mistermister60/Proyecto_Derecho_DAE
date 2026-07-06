<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request para almacenar un nuevo procurador.
 *
 * Valida los datos personales y profesionales del procurador.
 * La creación del usuario asociado se maneja en el controlador
 * dentro de una transacción.
 */
class StoreProcuradorRequest extends FormRequest
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
            'procurador_nombre' => 'required|string|max:30',
            'procurador_apellido' => 'required|string|max:30',
            'procurador_dni' => 'required|string|max:20|unique:procuradores,procurador_dni',
            'procurador_telefono' => 'required|string|max:15',
            'procurador_direccion' => 'required|string|max:350',
            'procurador_correo' => 'required|email|max:50|unique:procuradores,procurador_correo',
            'procurador_profesion' => 'required|string|max:50',
            'procurador_colegiacion' => 'required|string|max:50|unique:procuradores,procurador_colegiacion',
            'procurador_fecha_nacimiento' => 'required|date',
        ];
    }
}
