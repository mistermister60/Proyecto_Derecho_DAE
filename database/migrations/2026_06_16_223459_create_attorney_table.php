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
        Schema::create('attorney', function (Blueprint $table) {
            $table->id('attorney_id');
            $table->string('attorney_nom', 150);    
            $table->string('attorney_ape', 150);
            $table->string('attorney_dni', 19)->unique();
            $table->date('attorney_fecnac');
            $table->string('attorney_genero', 25);
            $table->string('attorney_email', 150)->unique();
            $table->string('attorney_tel', 29)->nullable();
            $table->string('attorney_dir', 200)->nullable();
            $table->string('attorney_estado', 25)->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attorney');
    }
};
