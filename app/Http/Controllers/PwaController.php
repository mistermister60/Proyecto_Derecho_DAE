<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePushNotificationRequest;
use App\Services\PwaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: PwaController
 * ═══════════════════════════════════════════════════════
 * Gestión de funcionalidades PWA (Progressive Web App):
 * - Clave pública VAPID para suscripciones push
 * - Suscripción/desuscripción a notificaciones push
 * - Panel de configuración de notificaciones
 * ───────────────────────────────────────────────────────
 * Delega la lógica de negocio en PwaService.
 * Rutas protegidas: middleware ['auth', 'otp', 'password.changed']
 * Prefijo de ruta: /api/notifications
 * Roles: Director y Procurador
 */
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
     * ═══════════════════════════════════════════════════════
     * getVapidPublicKey
     * ───────────────────────────────────────────────────────
     * Obtener clave pública VAPID para suscripción push.
     * Lee la clave de config/pwa.php, la convierte a base64url
     * (formato seguro para URL) y la retorna como JSON.
     * ═══════════════════════════════════════════════════════
     *
     * @return JsonResponse
     */
    public function getVapidPublicKey()
    {
        // ─── [Lectura de clave desde configuración] ────────────
        $publicKey = config('pwa.vapid.public_key');

        if (! $publicKey) {
            return response()->json(['error' => 'Vapid public key not configured'], 404);
        }

        // ─── [Conversión a base64url (formato seguro para URL)] ─
        // Reemplaza + / = por - _ (vacío) para URL-safe base64
        $urlSafeBase64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($publicKey));

        // ─── [Respuesta JSON] ──────────────────────────────────
        return response()->json(['publicKey' => $urlSafeBase64]);
    }

    /**
     * ═══════════════════════════════════════════════════════
     * subscribe
     * ───────────────────────────────────────────────────────
     * Suscribir usuario a notificaciones push.
     * Valida el formato de suscripción (JSON), decodifica y
     * delega en PwaService::subscribeToPush(). Retorna éxito
     * o error con logging de excepciones.
     * ═══════════════════════════════════════════════════════
     *
     * @param  StorePushNotificationRequest  $request  Suscripción push en JSON
     * @return JsonResponse
     */
    public function subscribe(StorePushNotificationRequest $request)
    {
        try {
            // ─── [Decodificación de suscripción] ────────────────
            $subscription = json_decode($request->input('subscription'), true);

            if (! is_array($subscription)) {
                return response()->json(['error' => 'Formato de suscripción inválido'], 422);
            }

            // ─── [Delegar suscripción al servicio] ──────────────
            $userId = auth()->id();
            $success = $this->pwaService->subscribeToPush($userId, $subscription);

            if ($success) {
                return response()->json(['message' => 'Suscripción push registrada exitosamente'], 200);
            }

            return response()->json(['error' => 'Fallo al suscribirse a push'], 500);

        } catch (\Exception $e) {
            // ─── [Logging de error] ─────────────────────────────
            Log::error('Error en suscripción push: '.$e->getMessage(), [
                'exception' => $e,
                'usuario_id' => auth()->id(),
            ]);

            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * ═══════════════════════════════════════════════════════
     * unsubscribe
     * ───────────────────────────────────────────────────────
     * Desuscribir usuario de notificaciones push.
     * Valida el formato, decodifica y delega en
     * PwaService::unsubscribeFromPush(). Retorna éxito o
     * error con logging de excepciones.
     * ═══════════════════════════════════════════════════════
     *
     * @param  StorePushNotificationRequest  $request  Suscripción push en JSON
     * @return JsonResponse
     */
    public function unsubscribe(StorePushNotificationRequest $request)
    {
        try {
            // ─── [Decodificación de suscripción] ────────────────
            $subscription = json_decode($request->input('subscription'), true);

            if (! is_array($subscription)) {
                return response()->json(['error' => 'Formato de suscripción inválido'], 422);
            }

            // ─── [Delegar desuscripción al servicio] ────────────
            $userId = auth()->id();
            $success = $this->pwaService->unsubscribeFromPush($userId, $subscription);

            if ($success) {
                return response()->json(['message' => 'Suscripción push eliminada exitosamente'], 200);
            }

            return response()->json(['error' => 'Fallo al eliminar suscripción push'], 500);

        } catch (\Exception $e) {
            // ─── [Logging de error] ─────────────────────────────
            Log::error('Error en desuscripción push: '.$e->getMessage(), [
                'exception' => $e,
                'usuario_id' => auth()->id(),
            ]);

            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * ═══════════════════════════════════════════════════════
     * notificationSettings
     * ───────────────────────────────────────────────────────
     * Mostrar panel de administración de notificaciones.
     * Obtiene la suscripción push actual del usuario y
     * renderiza la vista pwa.notifications con estado de
     * suscripción y ID de usuario.
     * ═══════════════════════════════════════════════════════
     *
     * @return View
     */
    public function notificationSettings()
    {
        // ─── [Obtener ID de usuario] ───────────────────────────
        $userId = request()->user()->usuario_id ?? 1;

        // ─── [Obtener suscripción actual] ──────────────────────
        $subscription = $this->pwaService->getUserPushSubscription($userId);
        $isSubscribed = ! empty($subscription);

        // ─── [Renderizado de vista] ────────────────────────────
        return view('pwa.notifications', compact('isSubscribed', 'userId'));
    }
}
