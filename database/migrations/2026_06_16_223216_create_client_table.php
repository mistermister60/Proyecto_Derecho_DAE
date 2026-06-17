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
        Schema::create('client', function (Blueprint $table) {
            $table->id('client_id');
            $table->string('client_nom', 150);
            $table->string('client_ape', 150);
            $table->string('client_dni', 19)->unique();
            $table->string('client_civil',200)->nullable();
            $table->string('client_tel', 29)->nullable();
            $table->string('client_dir', 200)->nullable();
            $table->string('client_numhij',5)->nullable();
            $table->string('client_nomhj',150)->nullable();
            $table->string('client_prof',200)->nullable();
            $table->string('client_lugarlab',350)->nullable();
            $table->string('client_dirlab',350)->nullable();
            $table->string('client_telab',29)->nullable();
            $table->decimal('client_salar', 20, 2)->nullable();
            $table->string('client_estado', 25)->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client');
    }
};
