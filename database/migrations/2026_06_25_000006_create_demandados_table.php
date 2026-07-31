<?php

/**
 * ═══════════════════════════════════════════════════════
 * MIGRACIÓN: demandados
 * ═══════════════════════════════════════════════════════
 * Crea la tabla 'demandados' que almacena los datos de la
 * parte demandada en cada caso jurídico. Permite registrar
 * información personal y laboral de la persona o entidad
 * contra quien se interpone la demanda.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Creación de tabla: demandados ─────────────
        // Datos personales y laborales de la parte demandada
        // Columnas: demandado_id, nombre, apellido, dni, estado_civil, telefono,
        //           direccion, profesion, lugar_trabajo, telefono_trabajo, estado, timestamps
        Schema::create('demandados', function (Blueprint $table) {
            // ─── Identificador primario ─────────────────
            $table->id('demandado_id');

            // ─── Datos personales ──────────────────────
            $table->string('demandado_nombre', 100);
            $table->string('demandado_apellido', 100);
            $table->string('demandado_dni', 19)->unique();
            $table->string('demandado_estado_civil', 50)->nullable();
            $table->string('demandado_telefono', 29)->nullable();
            $table->string('demandado_direccion', 200)->nullable();

            // ─── Datos laborales ───────────────────────
            $table->string('demandado_profesion', 200)->nullable();
            $table->string('demandado_lugar_trabajo', 350)->nullable();
            $table->string('demandado_telefono_trabajo', 29)->nullable();

            // ─── Estado del registro ───────────────────
            $table->string('demandado_estado', 25)->default('activo');

            // ─── Timestamps ────────────────────────────
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandados');
    }
};
