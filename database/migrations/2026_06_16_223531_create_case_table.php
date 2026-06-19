<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('case', function (Blueprint $table) {
            $table->id('cases_id');
            $table->string('cases_numeroexpediente', 100)->unique();
            $table->string('cases_usuario', 150);
            $table->string('cases_telefono', 29)->nullable();
            $table->string('cases_direccion', 200)->nullable();
            $table->string('cases_proceso', 260)->nullable();
            $table->string('cases_estadoactual',500)->nullable();
            $table->string('cases_pendientes',500)->nullable();
            $table->string('cases_documentos',500)->nullable();
            $table->string('cases_observ',500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case');
    }
};
