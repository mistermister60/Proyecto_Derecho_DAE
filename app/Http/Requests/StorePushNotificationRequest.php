<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request para almacenar una suscripción de notificación push.
 *
 * Valida que el payload de suscripción sea un string válido.
 * El usuario se obtiene de auth()->id() en el controlador.
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
        ];
    }
}
