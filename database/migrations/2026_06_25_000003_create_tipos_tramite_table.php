<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_tramite', function (Blueprint $table) {
            $table->id('tipo_tramite_id');
            $table->string('tramite_nombre', 150)->unique();
            $table->string('tramite_descripcion', 500)->nullable();
            $table->string('tramite_estado', 25)->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_tramite');
    }
};
