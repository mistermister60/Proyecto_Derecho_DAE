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
        Schema::create('interview', function (Blueprint $table) {
            $table->id('interview_id');
            $table->date('interview_date');
            $table->string('interview_hechos', 600);
            $table->bigInteger('tramit_id')->unsigned();
            $table->foreign('tramit_id')->references('tramit_id')->on('tramit');
            $table->bigInteger('attorney_id')->unsigned();
            $table->foreign('attorney_id')->references('attorney_id')->on('attorney');
            $table->string('interview_observ', 500)->nullable();
            $table->boolean('interview_adminsible')->default(true);
            $table->date('interview_dateassign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interview');
    }
};
