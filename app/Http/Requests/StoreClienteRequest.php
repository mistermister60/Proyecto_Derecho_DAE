<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request para almacenar un nuevo cliente.
 *
 * Valida los datos personales, laborales y familiares del cliente.
 * El campo nombre_completo se divide automáticamente en nombre y apellido
 * en el controlador.
 */
class StoreClienteRequest extends FormRequest
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
            'nombre_completo' => 'required|string|max:200',
            'cliente_dni' => 'required|string|max:20|unique:clientes,cliente_dni',
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

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre_completo.required' => 'El nombre completo del cliente es obligatorio.',
            'nombre_completo.max' => 'El nombre completo no puede tener más de :max caracteres.',
            'cliente_dni.required' => 'El número de DNI del cliente es obligatorio.',
            'cliente_dni.max' => 'El DNI no puede tener más de :max caracteres.',
            'cliente_dni.unique' => 'Este número de DNI ya está registrado en el sistema.',
            'cliente_estado_civil.required' => 'El estado civil del cliente es obligatorio.',
            'cliente_estado_civil.max' => 'El estado civil no puede tener más de :max caracteres.',
            'cliente_telefono.required' => 'El teléfono del cliente es obligatorio.',
            'cliente_telefono.max' => 'El teléfono no puede tener más de :max caracteres.',
            'cliente_direccion.required' => 'La dirección del cliente es obligatoria.',
            'cliente_numero_hijos.integer' => 'El número de hijos debe ser un valor entero.',
            'cliente_numero_hijos.min' => 'El número de hijos no puede ser negativo.',
            'cliente_nombres_hijos.max' => 'Los nombres de los hijos no pueden exceder :max caracteres.',
            'cliente_profesion.max' => 'La profesión no puede tener más de :max caracteres.',
            'cliente_lugar_trabajo.max' => 'El lugar de trabajo no puede tener más de :max caracteres.',
            'cliente_direccion_trabajo.max' => 'La dirección de trabajo no puede tener más de :max caracteres.',
            'cliente_telefono_trabajo.max' => 'El teléfono del trabajo no puede tener más de :max caracteres.',
            'cliente_salario_mensual.numeric' => 'El salario mensual debe ser un valor numérico.',
            'cliente_salario_mensual.min' => 'El salario mensual no puede ser negativo.',
        ];
    }
}
