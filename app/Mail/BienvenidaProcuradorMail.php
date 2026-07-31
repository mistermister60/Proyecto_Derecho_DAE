<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * ═══════════════════════════════════════════════════════
 * MAIL: BienvenidaProcuradorMail
 * ═══════════════════════════════════════════════════════
 * Email de bienvenida para Procuradores nuevos.
 *
 * Se envía cuando un Director crea un usuario con rol Procurador.
 * Incluye credenciales temporales y URL de login.
 */
class BienvenidaProcuradorMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombre,
        public string $email,
        public string $tempPassword,
        public string $loginUrl
    ) {}

    /**
     * Construir el mensaje de bienvenida para el procurador.
     *
     * Envía un email con plantilla Markdown que incluye:
     * - Nombre del procurador
     * - Email de acceso
     * - Contraseña temporal generada por el Director
     * - URL directa al formulario de login
     *
     * @return self Instancia del mailable configurada.
     */
    public function build(): self
    {
        return $this->subject('Bienvenido al Consultorio Jurídico DAE - Tus Credenciales de Acceso')
            ->markdown('emails.bienvenida-procurador', [
                'nombre' => $this->nombre,
                'email' => $this->email,
                'tempPassword' => $this->tempPassword,
                'loginUrl' => $this->loginUrl,
            ]);
    }
}
