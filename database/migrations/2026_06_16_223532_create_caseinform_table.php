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
        Schema::create('caseinform', function (Blueprint $table) {
            $table->id('caseinform_id');
            $table->date('caseinform_date');
            $table->bigInteger('attorney_id')->unsigned();
            $table->foreign('attorney_id')->references('attorney_id')->on('attorney');
            $table->string('caseinform_motivo', 250);
            $table->bigInteger('cases_id')->unsigned();
            $table->foreign('cases_id')->references('cases_id')->on('case');
            $table->string('caseinform_estado',25)->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caseinform');
    }
};
