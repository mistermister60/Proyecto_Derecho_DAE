<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * ═══════════════════════════════════════════════════════
 * MAIL: CodigoVerificacionMail
 * ═══════════════════════════════════════════════════════
 * Email con el código de verificación OTP (6 dígitos) para 2FA.
 *
 * Se envía durante el proceso de autenticación de dos factores
 * tras un login exitoso. El código expira a los 15 minutos.
 */
class CodigoVerificacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $codigo;

    public function __construct($codigo)
    {
        $this->codigo = $codigo;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu Código de Verificación de Acceso - Consultorio Jurídico DAE',
        );
    }

    /**
     * Construir el contenido del email con el código OTP.
     *
     * Define el asunto del correo y la vista Markdown que renderiza
     * el código de verificación de 6 dígitos para el usuario.
     *
     * @return Content Instancia de contenido con la vista del email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.codigo-verificacion',
        );
    }
}
