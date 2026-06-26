<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguimientos', function (Blueprint $table) {
            $table->id('seguimiento_id');
            $table->foreignId('caso_id')->constrained('casos', 'caso_id');
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios', 'usuario_id');
            $table->date('seguimiento_fecha');
            $table->string('seguimiento_tipo', 50)->default('sistema');
            $table->text('seguimiento_descripcion');
            $table->string('seguimiento_estado', 25)->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguimientos');
    }
};
