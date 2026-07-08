<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request para almacenar una suscripción de notificación push.
 *
 * Valida que el payload de suscripción sea un string válido y que
 * el usuario asociado exista en la tabla `usuarios`.
 */
class StorePushNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Las notificaciones push están permitidas para todos los usuarios autenticados.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'subscription' => ['required', 'string'],
            'user_id' => ['required', 'integer', 'exists:usuarios,usuario_id'],
        ];
    }

    /**
     * Get the custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'subscription.required' => 'La suscripción push es requerida',
            'subscription.string' => 'La suscripción push debe ser un string',
            'user_id.required' => 'El ID de usuario es requerido',
            'user_id.integer' => 'El ID de usuario debe ser un entero',
            'user_id.exists' => 'El usuario especificado no existe',
        ];
    }
}
