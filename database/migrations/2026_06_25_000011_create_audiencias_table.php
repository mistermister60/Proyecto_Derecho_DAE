<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audiencias', function (Blueprint $table) {
            $table->id('audiencia_id');
            $table->foreignId('caso_id')->constrained('casos', 'caso_id');
            $table->foreignId('procurador_id')->constrained('procuradores', 'procurador_id');
            $table->date('audiencia_fecha');
            $table->time('audiencia_hora')->nullable();
            $table->string('audiencia_juzgado', 50)->nullable();
            $table->string('audiencia_tipo', 100)->nullable();
            $table->string('audiencia_estado', 25)->default('pendiente');
            $table->text('audiencia_observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audiencias');
    }
};
