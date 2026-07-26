<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email de bienvenida para Procuradores nuevos.
 * 
 * Se envía cuando un Director crea un usuario con rol Procurador.
 * Incluye credenciales temporales y URL de login.
 */
class BienvenidaProcuradorMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombre,
        public string $email,
        public string $tempPassword,
        public string $loginUrl
    ) {}

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