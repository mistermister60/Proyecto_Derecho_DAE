<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procuradores', function (Blueprint $table) {
            $table->date('procurador_fecha_ingreso')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('procuradores', function (Blueprint $table) {
            $table->dropColumn('procurador_fecha_ingreso');
        });
    }
};