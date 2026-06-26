<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id('documento_id');
            $table->foreignId('caso_id')->constrained('casos', 'caso_id');
            $table->string('documento_nombre', 255);
            $table->string('documento_tipo', 10)->nullable();
            $table->string('documento_ruta', 500)->nullable();
            $table->string('documento_tamano', 20)->nullable();
            $table->text('documento_descripcion')->nullable();
            $table->string('documento_estado', 25)->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
