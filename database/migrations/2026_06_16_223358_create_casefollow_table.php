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
        Schema::create('casefollow', function (Blueprint $table) {
            $table->id('casefollow_id');
            $table->date('casefollow_date');
            $table->string('casefollow_noexpe', 250);
            $table->string('casefollow_juez', 100);
            $table->unsignedBigInteger('tramit_id');
            $table->foreign('tramit_id')->references('tramit_id')->on('tramit');
            $table->string('casefollow_parterepresent', 250);
            $table->string('casefollow_representtel', 29)->nullable();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('client_id')->on('client');
            $table->unsignedBigInteger('defendant_id');
            $table->foreign('defendant_id')->references('defendant_id')->on('defendant');
            $table->string('casefollow_observ', 500)->nullable();
            $table->string('casefollow_estado', 25)->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casefollow');
    }
};
