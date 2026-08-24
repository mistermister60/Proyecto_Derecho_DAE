<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * ═══════════════════════════════════════════════════════
 * SEEDER: ProcuradoresTemporalesSeeder
 * ═══════════════════════════════════════════════════════
 * Crea los 24 procuradores estudiantes reales del Consultorio
 * Jurídico DAE con su correo institucional (cuenta@usap.edu) y
 * su usuario de acceso cuya contraseña temporal es
 * 'Password123' y debe cambiarse en el primer inicio.
 *
 * Usa updateOrInsert para ser idempotente (re-seed sin duplicados).
 */
class ProcuradoresTemporalesSeeder extends Seeder
{
    public function run(): void
    {
        $procuradorRolId = DB::table('roles')->where('rol_nombre', 'Procurador')->value('rol_id');

        $estudiantes = [
            'Jaime Iván Rodríguez Bonilla' => '2180266',
            'Leily Victoria Martínez Yanes' => '1220297',
            'Shadya Maria Urquia Sánchez' => '1230052',
            'Emely Larios' => '2150224',
            'Fernando Samuel Paz Villanueva' => '2180040',
            'Lucia Yolanda Renee Umaña Villar' => '1171026',
            'Laura Melissa Delcid Alvarez' => '3160181',
            'Adriana Nicole Estevez Camacho' => '3220274',
            'Claudia Nicolle Nuñez Reconco' => '2190384',
            'Jonathan David Archaga Vásquez' => '1180115',
            'Génesis Estrella García Almendarez' => '1230194',
            'Sandra Yadira Gutierrez Flores' => '1210479',
            'Iris Fabiola Canales Paiz' => '1170488',
            'Yensy Pamela Cubas' => '2200075',
            'Katheryne Cruz' => '1190576',
            'Lucio Marcello Ferrera Reyes' => '2200300',
            'Greysi Mabel Ríos Murcia' => '1220185',
            'Angely Michelle Gonzales Torres' => '1210631',
            'Daniel Alberto Babun Marcos' => '3220455',
            'Juan Antonio Guevara Paz' => '1200517',
            'Marely Giordana Crespo Pinto' => '1200559',
            'Youssef Isai Pineda Fajardo' => '1201069',
            'Valerie Abigail Jimenez Arzú' => '2220090',
            'Brenda Flores' => '1894189',
        ];

        foreach ($estudiantes as $nombreCompleto => $cuenta) {
            $partes = explode(' ', $nombreCompleto, 2);
            $nombre = $partes[0];
            $apellido = $partes[1] ?? '';
            $email = $cuenta.'@usap.edu';
            $dniTemporal = 'TEMP-'.$cuenta;

            DB::table('procuradores')->updateOrInsert(
                ['procurador_email' => $email],
                [
                    'procurador_nombre' => $nombre,
                    'procurador_apellido' => $apellido,
                    'procurador_dni' => $dniTemporal,
                    'procurador_carnet' => $cuenta,
                    'procurador_fecha_nacimiento' => '2000-01-01',
                    'procurador_genero' => 'No especificado',
                    'procurador_email' => $email,
                    'procurador_telefono' => null,
                    'procurador_contacto_emergencia' => null,
                    'procurador_estado' => 'activo',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $procuradorId = DB::table('procuradores')->where('procurador_email', $email)->value('procurador_id');

            DB::table('usuarios')->updateOrInsert(
                ['email' => $email],
                [
                    'rol_id' => $procuradorRolId,
                    'procurador_id' => $procuradorId,
                    'usuario_nombre' => $nombreCompleto,
                    'email' => $email,
                    'contrasena' => Hash::make('Password123'),
                    'usuario_estado' => 'activo',
                    'debe_cambiar_contrasena' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
