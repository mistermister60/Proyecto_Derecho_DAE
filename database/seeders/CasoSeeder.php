<?php

namespace Database\Seeders;

use App\Models\Caso;
use App\Models\Cliente;
use App\Models\EstadoCaso;
use App\Models\Procurador;
use App\Models\TipoTramite;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CasoSeeder extends Seeder
{
    public function run(): void
    {
        $procuradores = Procurador::pluck('procurador_id')->toArray();
        $clientes = Cliente::pluck('cliente_id')->toArray();
        $estados = EstadoCaso::where('estado_tipo', 'pipeline')->pluck('estado_id', 'estado_nombre')->toArray();
        $tramites = TipoTramite::pluck('tipo_tramite_id')->toArray();

        $ultimo = Caso::orderBy('caso_id', 'desc')->first();
        $correlativo = $ultimo ? intval(substr($ultimo->caso_numero_expediente, -5)) : 0;

        $casosData = [
            ['cliente_idx' => 0, 'tramite' => 'Divorcio', 'estado' => 'Audiencia señalada'],
            ['cliente_idx' => 0, 'tramite' => 'Pensión', 'estado' => 'Presentación de demanda'],
            ['cliente_idx' => 1, 'tramite' => 'Divorcio', 'estado' => 'Audiencia señalada'],
            ['cliente_idx' => 1, 'tramite' => 'Propiedad', 'estado' => 'Resolución'],
            ['cliente_idx' => 2, 'tramite' => 'Lesiones', 'estado' => 'Sentencia'],
            ['cliente_idx' => 2, 'tramite' => 'Contrato', 'estado' => 'Entrevista'],
            ['cliente_idx' => 0, 'tramite' => 'Familia', 'estado' => 'Atrasado'],
            ['cliente_idx' => 3, 'tramite' => 'Divorcio', 'estado' => 'Audiencia señalada'],
            ['cliente_idx' => 3, 'tramite' => 'Pensión', 'estado' => 'Mediación'],
            ['cliente_idx' => 4, 'tramite' => 'Civil', 'estado' => 'Entrevista'],
        ];

        foreach ($casosData as $i => $data) {
            $correlativo++;
            $diasAtras = rand(10, 180);

            Caso::create([
                'caso_numero_expediente' => '0501-'.now()->year.'-'.str_pad($correlativo, 5, '0', STR_PAD_LEFT),
                'cliente_id' => $clientes[$data['cliente_idx'] % count($clientes)],
                'procurador_id' => $procuradores[array_rand($procuradores)],
                'estado_id' => $estados[$data['estado']] ?? array_values($estados)[0],
                'tipo_tramite_id' => $tramites[array_rand($tramites)],
                'caso_fecha_interpuesta' => Carbon::now()->subDays($diasAtras)->toDateString(),
                'caso_estado' => $i < 8 ? 'activo' : 'cerrado',
                'created_at' => Carbon::now()->subDays($diasAtras),
                'updated_at' => Carbon::now(),
            ]);
        }

        $this->command->info(' 10 casos demo creados correctamente.');
    }
}