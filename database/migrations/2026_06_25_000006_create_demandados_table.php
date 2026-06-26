<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandados', function (Blueprint $table) {
            $table->id('demandado_id');
            $table->string('demandado_nombre', 100);
            $table->string('demandado_apellido', 100);
            $table->string('demandado_dni', 19)->unique();
            $table->string('demandado_estado_civil', 50)->nullable();
            $table->string('demandado_telefono', 29)->nullable();
            $table->string('demandado_direccion', 200)->nullable();
            $table->string('demandado_profesion', 200)->nullable();
            $table->string('demandado_lugar_trabajo', 350)->nullable();
            $table->string('demandado_telefono_trabajo', 29)->nullable();
            $table->string('demandado_estado', 25)->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandados');
    }
};
