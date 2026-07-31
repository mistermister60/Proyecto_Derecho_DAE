<?php

/**
 * ═══════════════════════════════════════════════════════
 * MIGRACIÓN: casos
 * ═══════════════════════════════════════════════════════
 * Crea la tabla 'casos' que es la entidad central del
 * sistema. Cada caso vincula un cliente, un demandado
 * (opcional), un tipo de trámite, un estado del pipeline,
 * y un procurador asignado. Incluye campos para juzgado,
 * fechas, relación de hechos, observaciones del director
 * y admisibilidad.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Creación de tabla: casos ────────────────────
        // Entidad central: expedientes jurídicos del consultorio
        // Columnas: caso_id, caso_numero_expediente, cliente_id, demandado_id,
        //           tipo_tramite_id, estado_id, procurador_id, caso_parte_representada,
        //           caso_juzgado, caso_fecha_interpuesta, caso_relacion_hechos,
        //           caso_observaciones_director, caso_admisible, caso_fecha_asignacion,
        //           caso_estado, timestamps
        Schema::create('casos', function (Blueprint $table) {
            // ─── Identificador primario ─────────────────
            $table->id('caso_id');

            // ─── Número de expediente (único) ───────────
            $table->string('caso_numero_expediente', 50)->unique();

            // ─── Relaciones principales ─────────────────
            $table->foreignId('cliente_id')->constrained('clientes', 'cliente_id');
            $table->foreignId('demandado_id')->nullable()->constrained('demandados', 'demandado_id');
            $table->foreignId('tipo_tramite_id')->constrained('tipos_tramite', 'tipo_tramite_id');
            $table->foreignId('estado_id')->constrained('estados_caso', 'estado_id');
            $table->foreignId('procurador_id')->constrained('procuradores', 'procurador_id');

            // ─── Datos del caso ─────────────────────────
            $table->string('caso_parte_representada', 50)->nullable();
            $table->string('caso_juzgado', 50)->nullable();
            $table->date('caso_fecha_interpuesta')->nullable();
            $table->text('caso_relacion_hechos')->nullable();
            $table->text('caso_observaciones_director')->nullable();
            $table->boolean('caso_admisible')->nullable()->default(true);
            $table->date('caso_fecha_asignacion')->nullable();

            // ─── Estado del registro ───────────────────
            $table->string('caso_estado', 25)->default('activo');

            // ─── Timestamps ────────────────────────────
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('casos');
    }
};
