<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ═══════════════════════════════════════════════════════
 * MIGRACIÓN: procurador_contacto_emergencia
 * ═══════════════════════════════════════════════════════
 * Agrega el campo 'procurador_contacto_emergencia' a la
 * tabla 'procuradores' para que, en el primer inicio de
 * sesión, el procurador deba registrar su contacto de
 * emergencia (dato demográfico obligatorio).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procuradores', function (Blueprint $table) {
            $table->string('procurador_contacto_emergencia', 150)->nullable()
                ->after('procurador_telefono');
        });
    }

    public function down(): void
    {
        Schema::table('procuradores', function (Blueprint $table) {
            $table->dropColumn('procurador_contacto_emergencia');
        });
    }
};
