<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Facades\Log;

class PwaService
{
    /**
     * Suscribir usuario a notificaciones push
     */
    public function subscribeToPush(int $userId, array $subscription): bool
    {
        try {
            $usuario = Usuario::find($userId);

            if (! $usuario) {
                Log::warning("Intento de suscripción push para usuario no existente: {$userId}");

                return false;
            }

            $usuario->push_subscription = json_encode($subscription);
            $usuario->push_notification_token = $subscription['endpoint'] ?? null;
            $usuario->save();

            Log::info("Suscripción push registrada para usuario: {$userId}");

            return true;

        } catch (\Exception $e) {
            Log::error('Error al suscribir a push: '.$e->getMessage(), [
                'exception' => $e,
                'user_id' => $userId,
                'subscription' => $subscription,
            ]);

            return false;
        }
    }

    /**
     * Desuscribir usuario de notificaciones push
     */
    public function unsubscribeFromPush(int $userId, array $subscription): bool
    {
        try {
            $usuario = Usuario::find($userId);

            if (! $usuario) {
                return false;
            }

            if ($usuario->push_subscription) {
                $existingSubscription = json_decode($usuario->push_subscription, true);

                if ($existingSubscription && $existingSubscription['endpoint'] === $subscription['endpoint']) {
                    $usuario->push_subscription = null;
                    $usuario->push_notification_token = null;
                    $usuario->save();

                    Log::info("Suscripción push eliminada para usuario: {$userId}");

                    return true;
                }
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Error al eliminar suscripción push: '.$e->getMessage(), [
                'exception' => $e,
                'user_id' => $userId,
                'subscription' => $subscription,
            ]);

            return false;
        }
    }

    /**
     * Obtener suscripción push actual de usuario
     */
    public function getUserPushSubscription(int $userId): ?array
    {
        try {
            $usuario = Usuario::find($userId);

            if (! $usuario || ! $usuario->push_subscription) {
                return null;
            }

            return json_decode($usuario->push_subscription, true);

        } catch (\Exception $e) {
            Log::error('Error al obtener suscripción push: '.$e->getMessage(), [
                'exception' => $e,
                'user_id' => $userId,
            ]);

            return null;
        }
    }

    /**
     * Enviar notificación push a usuario específico
     */
    public function sendPushNotification(int $userId, array $notificationData): bool
    {
        try {
            $subscription = $this->getUserPushSubscription($userId);

            if (! $subscription) {
                Log::warning("No hay suscripción push para usuario: {$userId}");

                return false;
            }

            // Aquí se implementarían los servicios reales de push (SendPulse, OneSignal, etc.)
            // Por ahora, implementaramos logging real

            Log::info("Enviando notificación push a usuario: {$userId}", [
                'notification' => $notificationData,
                'subscription' => array_diff_key($subscription, ['auth' => null, 'salt' => null]),
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error al enviar notificación push: '.$e->getMessage(), [
                'exception' => $e,
                'user_id' => $userId,
                'notification' => $notificationData,
            ]);

            return false;
        }
    }

    /**
     * Enviar notificación push a múltiples usuarios
     */
    public function broadcastPushNotification(array $userIds, array $notificationData): int
    {
        $successCount = 0;

        foreach ($userIds as $userId) {
            if ($this->sendPushNotification($userId, $notificationData)) {
                $successCount++;
            }
        }

        Log::info("Broadcast push enviado a {$successCount} de ".count($userIds).' usuarios');

        return $successCount;
    }

    /**
     * Validar suscripción push
     */
    public function validateSubscription(array $subscription): bool
    {
        $requiredFields = ['endpoint', 'keys'];

        foreach ($requiredFields as $field) {
            if (! isset($subscription[$field])) {
                return false;
            }
        }

        if (! isset($subscription['keys']['p256dh']) || ! isset($subscription['keys']['auth'])) {
            return false;
        }

        return true;
    }
}
