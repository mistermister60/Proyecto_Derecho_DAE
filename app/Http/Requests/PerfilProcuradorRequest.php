<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ═══════════════════════════════════════════════════════
 * FORM REQUEST: Perfil Procurador (Completar perfil)
 * ═══════════════════════════════════════════════════════
 * Valida los datos demográficos obligatorios que el
 * procurador debe registrar en su primer inicio de sesión:
 * DNI, fecha de nacimiento, celular actual y contacto de
 * emergencia.
 */
class PerfilProcuradorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->procurador !== null;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        $procuradorId = $this->user()->procurador->procurador_id;

        return [
            'procurador_dni' => 'required|string|max:20|unique:procuradores,procurador_dni,'.$procuradorId.',procurador_id|not_regex:/^TEMP-/i',
            'procurador_fecha_nacimiento' => 'required|date',
            'procurador_telefono' => 'required|string|max:15',
            'procurador_contacto_emergencia' => 'required|string|max:150',
            'procurador_genero' => 'nullable|string|in:Masculino,Femenino',
            'procurador_direccion' => 'nullable|string|max:350',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'procurador_dni.required' => 'El DNI es obligatorio.',
            'procurador_dni.unique' => 'Este DNI ya está registrado en el sistema.',
            'procurador_dni.not_regex' => 'Debes ingresar tu DNI real (no el temporal).',
            'procurador_fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'procurador_telefono.required' => 'El número de celular actual es obligatorio.',
            'procurador_contacto_emergencia.required' => 'El contacto de emergencia es obligatorio.',
        ];
    }
}
