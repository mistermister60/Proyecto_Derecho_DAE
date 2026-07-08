<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request para actualizar un cliente existente.
 *
 * Valida los datos editables del cliente. Utiliza cliente_nombre y
 * cliente_apellido en lugar de nombre_completo. Excluye el DNI actual
 * de la validación única para permitir mantener el mismo valor.
 */
class UpdateClienteRequest extends FormRequest
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
            'cliente_nombre' => 'required|string|max:100',
            'cliente_apellido' => 'required|string|max:100',
            'cliente_dni' => 'required|string|max:20|unique:clientes,cliente_dni,'.$this->route('identidad').',cliente_dni',
            'cliente_estado_civil' => 'required|string|max:30',
            'cliente_telefono' => 'required|string|max:20',
            'cliente_direccion' => 'required|string',
            'cliente_numero_hijos' => 'nullable|integer|min:0',
            'cliente_nombres_hijos' => 'nullable|string|max:250',
            'cliente_profesion' => 'nullable|string|max:100',
            'cliente_lugar_trabajo' => 'nullable|string|max:100',
            'cliente_direccion_trabajo' => 'nullable|string|max:350',
            'cliente_telefono_trabajo' => 'nullable|string|max:29',
            'cliente_salario_mensual' => 'nullable|numeric|min:0',
        ];
    }
}
