<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('casos', function (Blueprint $table) {
            $table->id('caso_id');
            $table->string('caso_numero_expediente', 50)->unique();
            $table->foreignId('cliente_id')->constrained('clientes', 'cliente_id');
            $table->foreignId('demandado_id')->nullable()->constrained('demandados', 'demandado_id');
            $table->foreignId('tipo_tramite_id')->constrained('tipos_tramite', 'tipo_tramite_id');
            $table->foreignId('estado_id')->constrained('estados_caso', 'estado_id');
            $table->foreignId('procurador_id')->constrained('procuradores', 'procurador_id');
            $table->string('caso_parte_representada', 50)->nullable();
            $table->string('caso_juzgado', 50)->nullable();
            $table->date('caso_fecha_interpuesta')->nullable();
            $table->text('caso_relacion_hechos')->nullable();
            $table->text('caso_observaciones_director')->nullable();
            $table->boolean('caso_admisible')->nullable()->default(true);
            $table->date('caso_fecha_asignacion')->nullable();
            $table->string('caso_estado', 25)->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('casos');
    }
};
