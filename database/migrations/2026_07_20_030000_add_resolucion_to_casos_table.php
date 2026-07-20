<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            $table->string('resolucion_tipo', 25)->nullable()->after('caso_estado');
            $table->date('resolucion_fecha')->nullable()->after('resolucion_tipo');
            $table->text('resolucion_notas')->nullable()->after('resolucion_fecha');
        });
    }

    public function down(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            $table->dropColumn(['resolucion_tipo', 'resolucion_fecha', 'resolucion_notas']);
        });
    }
};
