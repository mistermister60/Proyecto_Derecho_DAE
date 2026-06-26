<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id('cliente_id');
            $table->string('cliente_nombre', 100);
            $table->string('cliente_apellido', 100);
            $table->string('cliente_dni', 19)->unique();
            $table->string('cliente_estado_civil', 50)->nullable();
            $table->string('cliente_telefono', 29)->nullable();
            $table->string('cliente_direccion', 200)->nullable();
            $table->integer('cliente_numero_hijos')->nullable()->default(0);
            $table->string('cliente_nombres_hijos', 250)->nullable();
            $table->string('cliente_profesion', 200)->nullable();
            $table->string('cliente_lugar_trabajo', 350)->nullable();
            $table->string('cliente_direccion_trabajo', 350)->nullable();
            $table->string('cliente_telefono_trabajo', 29)->nullable();
            $table->decimal('cliente_salario_mensual', 20, 2)->nullable();
            $table->string('cliente_estado', 25)->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
