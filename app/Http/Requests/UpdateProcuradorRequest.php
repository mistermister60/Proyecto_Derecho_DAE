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
            'procurador_email' => 'required|email|max:50|unique:procuradores,procurador_email,'.$identidad.',procurador_dni',
            'procurador_fecha_nacimiento' => 'required|date',
            'procurador_genero' => 'required|string|in:Masculino,Femenino',
            'procurador_carnet' => 'nullable|string|max:50|unique:procuradores,procurador_carnet,'.$identidad.',procurador_dni',
            'procurador_estado' => 'required|string|in:activo,inactivo',
            'procurador_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'procurador_fecha_ingreso' => ['required', 'date'],
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
            'procurador_nombre.required' => 'El nombre del procurador es obligatorio.',
            'procurador_nombre.max' => 'El nombre no puede tener más de :max caracteres.',
            'procurador_apellido.required' => 'El apellido del procurador es obligatorio.',
            'procurador_apellido.max' => 'El apellido no puede tener más de :max caracteres.',
            'procurador_dni.required' => 'El número de DNI es obligatorio.',
            'procurador_dni.max' => 'El DNI no puede tener más de :max caracteres.',
            'procurador_dni.unique' => 'Este número de DNI ya está registrado en el sistema.',
            'procurador_telefono.required' => 'El teléfono o celular es obligatorio.',
            'procurador_telefono.max' => 'El teléfono no puede tener más de :max caracteres.',
            'procurador_direccion.required' => 'La dirección es obligatoria.',
            'procurador_direccion.max' => 'La dirección no puede tener más de :max caracteres.',
            'procurador_email.required' => 'El correo electrónico es obligatorio.',
            'procurador_email.email' => 'Debe ingresar un correo electrónico válido.',
            'procurador_email.max' => 'El correo electrónico no puede tener más de :max caracteres.',
            'procurador_email.unique' => 'Este correo electrónico ya está registrado en el sistema.',
            'procurador_fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'procurador_fecha_nacimiento.date' => 'Debe ingresar una fecha de nacimiento válida.',
            'procurador_genero.required' => 'Debe seleccionar un género.',
            'procurador_genero.in' => 'El género seleccionado no es válido. Seleccione Masculino o Femenino.',
            'procurador_carnet.max' => 'El carnet profesional no puede tener más de :max caracteres.',
            'procurador_carnet.unique' => 'Este número de carnet profesional ya está registrado.',
            'procurador_estado.required' => 'Debe seleccionar un estado.',
            'procurador_estado.in' => 'El estado seleccionado no es válido.',
            'procurador_foto.image' => 'El archivo debe ser una imagen.',
            'procurador_foto.mimes' => 'La foto debe estar en formato JPEG o PNG.',
            'procurador_foto.max' => 'La foto no puede superar los 2 MB.',
            'procurador_fecha_ingreso.required' => 'La fecha de inicio de práctica es obligatoria.',
'procurador_fecha_ingreso.date' => 'La fecha de inicio de práctica debe ser una fecha válida.',
        ];
    }
}