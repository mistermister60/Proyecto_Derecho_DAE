<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrevistas', function (Blueprint $table) {
            $table->id('entrevista_id');
            $table->foreignId('caso_id')->constrained('casos', 'caso_id');
            $table->foreignId('procurador_id')->constrained('procuradores', 'procurador_id');
            $table->date('entrevista_fecha');
            $table->text('entrevista_relacion_hechos')->nullable();
            $table->text('entrevista_observaciones')->nullable();
            $table->string('entrevista_estado', 25)->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrevistas');
    }
};
