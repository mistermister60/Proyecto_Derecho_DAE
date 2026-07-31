<?php

/**
 * ═══════════════════════════════════════════════════════
 * MIGRACIÓN: estados_caso
 * ═══════════════════════════════════════════════════════
 * Crea la tabla 'estados_caso' que define el flujo de
 * estados (pipeline) por el que pasa un caso jurídico.
 * Incluye orden de progresión, color para visualización
 * y tipo de estado para segmentación del pipeline.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Creación de tabla: estados_caso ───────────
        // Catálogo de estados del pipeline de casos
        // Columnas: estado_id, estado_nombre, estado_orden, estado_color, estado_tipo, estado_estado, timestamps
        Schema::create('estados_caso', function (Blueprint $table) {
            // ─── Identificador primario ─────────────────
            $table->id('estado_id');

            // ─── Datos del estado ──────────────────────
            $table->string('estado_nombre', 100)->unique();
            $table->integer('estado_orden')->default(0);

            // ─── Personalización visual ───────────────
            $table->string('estado_color', 7)->default('#9CA3AF');

            // ─── Tipo de estado (pipeline/proceso) ────
            $table->string('estado_tipo', 25)->default('pipeline');

            // ─── Estado del registro ───────────────────
            $table->string('estado_estado', 25)->default('activo');

            // ─── Timestamps ────────────────────────────
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estados_caso');
    }
};
