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
        Schema::table('procuradores', function (Blueprint $table) {
            $table->string('procurador_foto', 500)->nullable()->after('procurador_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('procuradores', function (Blueprint $table) {
            //
        });
    }
};
