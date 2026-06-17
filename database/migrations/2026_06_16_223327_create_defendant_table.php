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
        Schema::create('defendant', function (Blueprint $table) {
            $table->id('defendant_id');
            $table->string('defendant_nom', 150);
            $table->string('defendant_ape', 150);
            $table->string('defendant_dni', 19)->unique();
            $table->string('defendant_civil',200)->nullable();
            $table->string('defendant_tel', 29)->nullable();
            $table->string('defendant_dir', 200)->nullable();
            $table->string('defendant_prof',200)->nullable();
            $table->string('defendant_lugarlab',350)->nullable();
            $table->string('defendant_dirlab',350)->nullable();
            $table->string('defendant_telab',29)->nullable();
            $table->string('defendant_estado', 25)->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defendant');
    }
};
