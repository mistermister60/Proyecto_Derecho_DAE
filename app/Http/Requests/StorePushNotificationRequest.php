<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ═══════════════════════════════════════════════════════
 * FORM REQUEST: Store Push Notification (Suscripción Push)
 * ═══════════════════════════════════════════════════════
 * Valida el payload de suscripción a notificaciones push (Web Push).
 * El usuario autenticado se obtiene en el controlador vía auth()->id().
 */
class StorePushNotificationRequest extends FormRequest
{
    /**
     * ═══════════════════════════════════════════════
     * AUTORIZACIÓN
     * ───────────────────────────────────────────────
     * Cualquier usuario autenticado puede suscribirse a notificaciones push.
     * El middleware 'auth' ya garantiza que hay sesión activa.
     * ═══════════════════════════════════════════════
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * ═══════════════════════════════════════════════
     * REGLAS DE VALIDACIÓN
     * ───────────────────────────────────────────────
     * - subscription: Obligatorio, string (JSON serializado del objeto PushSubscription)
     * ═══════════════════════════════════════════════
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'subscription' => ['required', 'string'],       // ─── Obligatorio, JSON serializado de la suscripción push
        ];
    }

    // ─── Mensajes personalizados de error ───────────────────────────────
    // Traduce los errores de validación a español para el usuario final.
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
