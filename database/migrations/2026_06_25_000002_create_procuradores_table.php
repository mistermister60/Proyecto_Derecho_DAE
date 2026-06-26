<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procuradores', function (Blueprint $table) {
            $table->id('procurador_id');
            $table->string('procurador_nombre', 100);
            $table->string('procurador_apellido', 100);
            $table->string('procurador_dni', 19)->unique();
            $table->string('procurador_carnet', 20)->unique()->nullable();
            $table->date('procurador_fecha_nacimiento');
            $table->string('procurador_genero', 25);
            $table->string('procurador_email', 150)->unique();
            $table->string('procurador_telefono', 29)->nullable();
            $table->string('procurador_direccion', 200)->nullable();
            $table->string('procurador_estado', 25)->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procuradores');
    }
};
