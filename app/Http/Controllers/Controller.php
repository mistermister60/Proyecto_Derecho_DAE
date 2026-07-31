<?php

namespace App\Http\Controllers;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: Controller (Base)
 * ═══════════════════════════════════════════════════════
 * Clase base abstracta para todos los controladores del sistema DAE.
 * Proporciona la infraestructura común que heredan el resto de controladores
 * de la aplicación. No incluye funcionalidad concreta.
 *
 * Herencia: Todos los controladores extienden esta clase.
 * Middleware común aplicado en routes/web.php: auth, otp, password.changed
 */
abstract class Controller
{
    //
}
