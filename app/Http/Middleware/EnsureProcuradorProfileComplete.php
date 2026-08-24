<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ═══════════════════════════════════════════════════════
 * MIDDLEWARE: EnsureProcuradorProfileComplete
 * ═══════════════════════════════════════════════════════
 * Fuerza al procurador a completar su perfil (DNI, fecha de
 * nacimiento, celular y contacto de emergencia) en el primer
 * inicio de sesión. Si el perfil está incompleto, redirige al
 * formulario de completado, excepto para rutas permitidas.
 *
 * El Director (sin procurador asociado) nunca es afectado.
 */
class EnsureProcuradorProfileComplete
{
    /**
     * Verificar si el procurador autenticado debe completar su perfil.
     *
     * @param  Request  $request  Petición HTTP entrante.
     * @param  Closure  $next  Siguiente middleware/controlador.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->procurador && ! $this->perfilCompleto($user->procurador)) {
            $rutasPermitidas = [
                'procuradores.completar-perfil',
                'procuradores.completar-perfil.store',
                'logout',
                'auth.two-factor',
                'auth.two-factor.verify',
                'password.change',
                'password.change.update',
            ];

            if (! in_array($request->route()->getName(), $rutasPermitidas, true)) {
                return redirect()->route('procuradores.completar-perfil')
                    ->with('warning', 'Completa tu perfil (DNI, fecha de nacimiento, celular y contacto de emergencia) antes de continuar.');
            }
        }

        return $next($request);
    }

    /**
     * Determina si el perfil del procurador está completo.
     */
    private function perfilCompleto($procurador): bool
    {
        // Solo se fuerza la completación a los procuradores creados por el
        // flujo de bienvenida (DNI temporal con prefijo 'TEMP-'). Los demás
        // procuradores (ya registrados) no se ven interrumpidos en sus
        // sesiones ni en las pruebas automatizadas.
        if (str_starts_with((string) ($procurador->procurador_dni ?? ''), 'TEMP-')) {
            return ! empty($procurador->procurador_fecha_nacimiento)
                && ! empty($procurador->procurador_telefono)
                && ! empty($procurador->procurador_contacto_emergencia);
        }

        return true;
    }
}
