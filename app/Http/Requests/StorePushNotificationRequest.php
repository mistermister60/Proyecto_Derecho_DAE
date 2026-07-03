<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePushNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Push notifications are allowed for all authenticated users
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'subscription' => ['required', 'string'],
            'user_id' => ['required', 'integer', 'exists:usuarios,usuario_id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
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
