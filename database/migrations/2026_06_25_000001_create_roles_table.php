<?php

/**
 * ═══════════════════════════════════════════════════════
 * MIGRACIÓN: roles
 * ═══════════════════════════════════════════════════════
 * Crea la tabla 'roles' que define los roles del sistema
 * (Director, Procurador, etc.). Cada rol tiene un nombre
 * único, descripción opcional y estado para activación/
 * desactivación lógica.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Creación de tabla: roles ──────────────────
        // Columnas: rol_id, rol_nombre, rol_descripcion, rol_estado, timestamps
        Schema::create('roles', function (Blueprint $table) {
            // ─── Identificador primario ─────────────────
            $table->id('rol_id');

            // ─── Datos del rol ─────────────────────────
            $table->string('rol_nombre', 60)->unique();
            $table->string('rol_descripcion', 255)->nullable();

            // ─── Estado del registro (activo/inactivo) ─
            $table->string('rol_estado', 25)->default('activo');

            // ─── Timestamps ────────────────────────────
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
