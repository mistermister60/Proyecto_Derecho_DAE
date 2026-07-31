<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ═══════════════════════════════════════════════════════
 * FORM REQUEST: Update Procurador (Actualización de Procurador)
 * ═══════════════════════════════════════════════════════
 * Valida los campos editables del procurador. Excluye el registro actual
 * de las validaciones únicas (DNI, correo, colegiación) para permitir
 * mantener los mismos valores.
 */
class UpdateProcuradorRequest extends FormRequest
{
    /**
     * ═══════════════════════════════════════════════
     * AUTORIZACIÓN
     * ───────────────────────────────────────────────
     * Solo usuarios autenticados pueden actualizar procuradores.
     * Verifica que exista una sesión activa mediante auth()->check().
     * ═══════════════════════════════════════════════
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * ═══════════════════════════════════════════════
     * REGLAS DE VALIDACIÓN
     * ───────────────────────────────────────────────
     * - procurador_nombre:          Obligatorio, texto, máx. 30 caracteres
     * - procurador_apellido:        Obligatorio, texto, máx. 30 caracteres
     * - procurador_dni:             Obligatorio, texto, máx. 20, único (excluye actual)
     * - procurador_telefono:        Obligatorio, texto, máx. 15 caracteres
     * - procurador_direccion:       Obligatorio, texto, máx. 350 caracteres
     * - procurador_email:           Obligatorio, email, máx. 50, único (excluye actual)
     * - procurador_fecha_nacimiento: Obligatorio, fecha válida
     * - procurador_genero:          Obligatorio, valores: Masculino|Femenino
     * - procurador_carnet:          Opcional, texto, máx. 50, único (excluye actual)
     * - procurador_estado:          Obligatorio, valores: activo|inactivo
     * - procurador_foto:            Opcional, imagen JPEG/PNG, máx. 2MB
     * - procurador_fecha_ingreso:   Obligatorio, fecha válida
     * ═══════════════════════════════════════════════
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        $identidad = $this->route('identidad');

        return [
            'procurador_nombre' => 'required|string|max:30',                                                                                              // ─── Obligatorio, nombre (máx. 30 caracteres)
            'procurador_apellido' => 'required|string|max:30',                                                                                            // ─── Obligatorio, apellido (máx. 30 caracteres)
            'procurador_dni' => 'required|string|max:20|unique:procuradores,procurador_dni,'.$identidad.',procurador_dni',                                // ─── Obligatorio, DNI único (excluye actual)
            'procurador_telefono' => 'required|string|max:15',                                                                                            // ─── Obligatorio, teléfono (máx. 15 caracteres)
            'procurador_direccion' => 'required|string|max:350',                                                                                          // ─── Obligatorio, dirección (máx. 350 caracteres)
            'procurador_email' => 'required|email|max:50|unique:procuradores,procurador_email,'.$identidad.',procurador_dni',                             // ─── Obligatorio, email único (excluye actual)
            'procurador_fecha_nacimiento' => 'required|date',                                                                                             // ─── Obligatorio, fecha de nacimiento válida
            'procurador_genero' => 'required|string|in:Masculino,Femenino',                                                                               // ─── Obligatorio, solo Masculino o Femenino
            'procurador_carnet' => 'nullable|string|max:50|unique:procuradores,procurador_carnet,'.$identidad.',procurador_dni',                          // ─── Opcional, carnet profesional único (excluye actual)
            'procurador_estado' => 'required|string|in:activo,inactivo',                                                                                  // ─── Obligatorio, activo o inactivo
            'procurador_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',                                                                            // ─── Opcional, imagen JPEG/PNG, máximo 2MB
            'procurador_fecha_ingreso' => ['required', 'date'],                                                                                           // ─── Obligatorio, fecha de inicio en la práctica
        ];
    }

    // ─── Mensajes personalizados de error ───────────────────────────────
    // Traduce los errores de validación a español para el usuario final.
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
