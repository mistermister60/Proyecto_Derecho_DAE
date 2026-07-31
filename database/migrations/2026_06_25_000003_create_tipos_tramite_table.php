<?php

/**
 * ═══════════════════════════════════════════════════════
 * MIGRACIÓN: tipos_tramite
 * ═══════════════════════════════════════════════════════
 * Crea la tabla 'tipos_tramite' con el catálogo de tipos
 * de trámite jurídico que puede tener un caso (ej. divorcio,
 * pensión alimenticia, sucesión, etc.). Cada caso se asocia
 * a un tipo de trámite específico.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Creación de tabla: tipos_tramite ──────────
        // Catálogo de tipos de trámite jurídico
        // Columnas: tipo_tramite_id, tramite_nombre, tramite_descripcion, tramite_estado, timestamps
        Schema::create('tipos_tramite', function (Blueprint $table) {
            // ─── Identificador primario ─────────────────
            $table->id('tipo_tramite_id');

            // ─── Datos del tipo de trámite ─────────────
            $table->string('tramite_nombre', 150)->unique();
            $table->string('tramite_descripcion', 500)->nullable();

            // ─── Estado del registro ───────────────────
            $table->string('tramite_estado', 25)->default('activo');

            // ─── Timestamps ────────────────────────────
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_tramite');
    }
};
