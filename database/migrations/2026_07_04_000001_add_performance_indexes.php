<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // casos.caso_estado: filtrado en index y dashboard
        // casos.procurador_id: scoping por procurador en cada query de listado
        Schema::table('casos', function (Blueprint $table) {
            $table->index('caso_estado', 'idx_casos_estado');
            $table->index('procurador_id', 'idx_casos_procurador');
        });

        // audiencias.audiencia_fecha: ordenamiento y filtrado de agenda
        Schema::table('audiencias', function (Blueprint $table) {
            $table->index('audiencia_fecha', 'idx_audiencias_fecha');
        });

        // clientes.cliente_estado: filtrado en selects de create/edit de casos
        Schema::table('clientes', function (Blueprint $table) {
            $table->index('cliente_estado', 'idx_clientes_estado');
        });
    }

    public function down(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            $table->dropIndex('idx_casos_estado');
            $table->dropIndex('idx_casos_procurador');
        });

        Schema::table('audiencias', function (Blueprint $table) {
            $table->dropIndex('idx_audiencias_fecha');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropIndex('idx_clientes_estado');
        });
    }
};
