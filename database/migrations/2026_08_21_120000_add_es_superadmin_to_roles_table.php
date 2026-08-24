<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ═══════════════════════════════════════════════════════
 * MIGRACIÓN: es_superadmin en roles
 * ═══════════════════════════════════════════════════════
 * Agrega el campo 'es_superadmin' a la tabla 'roles' para
 * identificar roles con privilegios de superadministrador
 * (ej. Director) directamente desde la base de datos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('es_superadmin')->default(false)->after('rol_estado');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('es_superadmin');
        });
    }
};
