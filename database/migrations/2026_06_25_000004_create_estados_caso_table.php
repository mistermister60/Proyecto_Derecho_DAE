<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estados_caso', function (Blueprint $table) {
            $table->id('estado_id');
            $table->string('estado_nombre', 100)->unique();
            $table->integer('estado_orden')->default(0);
            $table->string('estado_color', 7)->default('#9CA3AF');
            $table->string('estado_tipo', 25)->default('pipeline');
            $table->string('estado_estado', 25)->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estados_caso');
    }
};
