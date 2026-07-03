<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePushNotificationRequest;
use App\Services\PwaService;
use Illuminate\Support\Facades\Log;

class PwaController extends Controller
{
    /**
     * @var PwaService
     */
    protected $pwaService;

    public function __construct(PwaService $pwaService)
    {
        $this->pwaService = $pwaService;
    }

    /**
     * Obtener clave pública VAPID para suscripción push
     */
    public function getVapidPublicKey()
    {
        $publicKey = config('pwa.vapid.public_key');

        if (! $publicKey) {
            return response()->json(['error' => 'Vapid public key not configured'], 404);
        }

        // Convertir Uint8Array a base64url
        $urlSafeBase64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($publicKey));

        return response()->json(['publicKey' => $urlSafeBase64]);
    }

    /**
     * Suscribir usuario a notificaciones push
     */
    public function subscribe(StorePushNotificationRequest $request)
    {
        try {
            $subscription = json_decode($request->input('subscription'), true);

            if (! is_array($subscription)) {
                return response()->json(['error' => 'Formato de suscripción inválido'], 422);
            }

            $userId = $request->input('user_id');

            $success = $this->pwaService->subscribeToPush($userId, $subscription);

            if ($success) {
                return response()->json(['message' => 'Suscripción push registrada exitosamente'], 200);
            }

            return response()->json(['error' => 'Fallo al suscribirse a push'], 500);

        } catch (\Exception $e) {
            Log::error('Error en suscripción push: '.$e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Desuscribir usuario de notificaciones push
     */
    public function unsubscribe(StorePushNotificationRequest $request)
    {
        try {
            $subscription = json_decode($request->input('subscription'), true);

            if (! is_array($subscription)) {
                return response()->json(['error' => 'Formato de suscripción inválido'], 422);
            }

            $userId = $request->input('user_id');

            $success = $this->pwaService->unsubscribeFromPush($userId, $subscription);

            if ($success) {
                return response()->json(['message' => 'Suscripción push eliminada exitosamente'], 200);
            }

            return response()->json(['error' => 'Fallo al eliminar suscripción push'], 500);

        } catch (\Exception $e) {
            Log::error('Error en desuscripción push: '.$e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Mostrar panel de administración de notificaciones
     */
    public function notificationSettings()
    {
        $userId = request()->user()->usuario_id ?? 1;
        $subscription = $this->pwaService->getUserPushSubscription($userId);

        $isSubscribed = ! empty($subscription);

        return view('pwa.notifications', compact('isSubscribed', 'userId'));
    }
}
