<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * ═══════════════════════════════════════════════════════
 * MAIL: ResetPasswordMail
 * ═══════════════════════════════════════════════════════
 * Email para restablecimiento de contraseña.
 *
 * Se envía cuando un usuario solicita recuperar su contraseña.
 * Incluye el token de restablecimiento y datos del usuario.
 */
class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    public $nombre;

    public $email;

    public function __construct($token, $nombre, $email)
    {
        $this->token = $token;
        $this->nombre = $nombre;
        $this->email = $email;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Restablecimiento de Contraseña - Consultorio Jurídico DAE',
        );
    }

    /**
     * Construir el contenido del email de restablecimiento.
     *
     * Define el asunto del correo y la vista Markdown que renderiza
     * el enlace con el token para que el usuario defina una nueva contraseña.
     *
     * @return Content Instancia de contenido con la vista del email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
        );
    }
}
