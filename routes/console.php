<?php

use App\Mail\BienvenidaProcuradorMail;
use App\Models\Procurador;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command(
    'procuradores:enviar-bienvenida
        {--dry-run : No envía correos, solo lista los destinatarios}
        {--dni= : Filtra por DNI o correo (ej. 2180266 o 2180266@usap.edu)}
        {--force : Envía aunque el procurador ya completó su perfil}',
    function () {
        $dryRun = $this->option('dry-run');
        $dni = $this->option('dni');
        $force = $this->option('force');

        $query = Procurador::query();

        if ($dni) {
            $query->where(function ($q) use ($dni) {
                $q->where('procurador_dni', $dni)->orWhere('procurador_email', $dni);
            });
        }

        $procuradores = $query->get();

        if ($procuradores->isEmpty()) {
            $this->warn('No se encontraron procuradores para enviar.');

            return 0;
        }

        $enviados = 0;
        $omitidos = 0;

        foreach ($procuradores as $procurador) {
            $usuario = $procurador->usuario;

            if (! $force && $usuario && $usuario->debe_cambiar_contrasena === false) {
                $omitidos++;
                $this->line("Omitido (ya completó perfil): {$procurador->procurador_email}");

                continue;
            }

            if ($dryRun) {
                $this->info("[DRY-RUN] Se enviaría bienvenida a: {$procurador->procurador_email}");
                $enviados++;

                continue;
            }

            Mail::to($procurador->procurador_email)->send(
                new BienvenidaProcuradorMail(
                    $procurador->nombre_completo,
                    $procurador->procurador_email,
                    'Password123',
                    route('login')
                )
            );

            $enviados++;
            $this->info("Enviado a: {$procurador->procurador_email}");
        }

        $this->newLine();
        $this->info(
            'Resumen: '.$enviados.' enviados'
            .($omitidos ? ', '.$omitidos.' omitidos (ya completaron perfil)' : '')
            .($dryRun ? ' [MODO DRY-RUN]' : '')
        );

        return 0;
    }
)->purpose('Envía el correo de bienvenida con credenciales a los procuradores');
