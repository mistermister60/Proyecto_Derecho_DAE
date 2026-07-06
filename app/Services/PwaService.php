<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Facades\Log;

/**
 * PwaService — Servicio de notificaciones push para la PWA del sistema DAE.
 *
 * Gestiona el ciclo de vida completo de las suscripciones push del navegador:
 * alta, baja, consulta y envío de notificaciones. Las suscripciones se
 * almacenan como JSON en el modelo Usuario junto con el endpoint del servicio
 * push (tipo SendPulse, OneSignal, etc.).
 *
 * Incluye validación de la estructura de suscripción según el estándar
 * Web Push Protocol (endpoint + claves p256dh y auth), y soporte para
 * broadcast a múltiples usuarios simultáneamente.
 */
class PwaService
{
    /**
     * Suscribir un usuario a notificaciones push del navegador.
     *
     * Almacena la suscripción completa como JSON en el campo `push_subscription`
     * y el endpoint en `push_notification_token` del modelo Usuario.
     * Si el usuario no existe, registra una advertencia en logs y retorna false.
     *
     * @param  int  $userId  ID del usuario a suscribir.
     * @param  array  $subscription  Array con endpoint y keys de la suscripción push.
     * @return bool True si se registró correctamente, false en caso contrario.
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
     * Eliminar la suscripción push de un usuario.
     *
     * Verifica que la suscripción existente coincida con el endpoint
     * proporcionado antes de eliminar los campos. Si no coincide o no
     * existe suscripción activa, retorna false sin errores.
     *
     * @param  int  $userId  ID del usuario a desuscribir.
     * @param  array  $subscription  Array con el endpoint a eliminar.
     * @return bool True si se eliminó correctamente, false en caso contrario.
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
     * Obtener la suscripción push activa de un usuario.
     *
     * Decodifica y retorna el JSON almacenado en `push_subscription`.
     * Si el usuario no existe o no tiene suscripción, retorna null.
     *
     * @param  int  $userId  ID del usuario a consultar.
     * @return array|null Array con la suscripción decodificada, o null si no existe.
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
     * Enviar una notificación push a un usuario específico.
     *
     * Por ahora registra la notificación en logs como placeholder.
     * En producción se integrará con un proveedor real (SendPulse, OneSignal,
     * WebPush PHP, Firebase Cloud Messaging) que despache la notificación
     * al navegador del usuario a través del endpoint almacenado.
     *
     * @param  int  $userId  ID del usuario destino.
     * @param  array  $notificationData  Datos de la notificación (título, cuerpo, icono, URL, etc.).
     * @return bool True si el proceso fue exitoso (sin errores).
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
     * Enviar una notificación push a múltiples usuarios (broadcast).
     *
     * Itera sobre la lista de IDs de usuario e invoca sendPushNotification
     * para cada uno. Retorna la cantidad de envíos exitosos para que el
     * llamante pueda determinar si hubo fallos parciales.
     *
     * @param  array<int>  $userIds  Lista de IDs de usuarios destino.
     * @param  array  $notificationData  Datos de la notificación a enviar.
     * @return int Cantidad de notificaciones enviadas exitosamente.
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
     * Validar que una suscripción push tenga la estructura mínima requerida.
     *
     * Según el Web Push Protocol, una suscripción válida debe contener:
     * - `endpoint`: URL del servicio push.
     * - `keys.p256dh`: Clave pública P-256 para cifrado.
     * - `keys.auth`: Secreto de autenticación compartido.
     *
     * Si falta alguno de estos campos, la suscripción se considera inválida
     * y no debe almacenarse ni utilizarse para envíos.
     *
     * @param  array  $subscription  Array con los datos de la suscripción a validar.
     * @return bool True si la estructura es válida, false en caso contrario.
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
