<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reasignaciones', function (Blueprint $table) {
            $table->id('reasignacion_id');
            $table->foreignId('caso_id')->constrained('casos', 'caso_id');
            $table->foreignId('procurador_origen_id')->constrained('procuradores', 'procurador_id');
            $table->foreignId('procurador_destino_id')->constrained('procuradores', 'procurador_id');
            $table->date('reasignacion_fecha');
            $table->string('reasignacion_motivo', 250)->nullable();
            $table->text('reasignacion_observaciones')->nullable();
            $table->string('reasignacion_estado', 25)->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reasignaciones');
    }
};
