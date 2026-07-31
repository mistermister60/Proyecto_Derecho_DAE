<?php

/**
 * ═══════════════════════════════════════════════════════
 * MIGRACIÓN: clientes
 * ═══════════════════════════════════════════════════════
 * Crea la tabla 'clientes' que almacena los datos personales,
 * laborales y familiares de los clientes representados por
 * el consultorio jurídico. Un cliente puede tener uno o más
 * casos asociados en el sistema.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Creación de tabla: clientes ───────────────
        // Datos personales, laborales y familiares del cliente
        // Columnas: cliente_id, nombre, apellido, dni, estado_civil, telefono, direccion,
        //           num_hijos, nombres_hijos, profesion, lugar_trabajo, direccion_trabajo,
        //           telefono_trabajo, salario_mensual, estado, timestamps
        Schema::create('clientes', function (Blueprint $table) {
            // ─── Identificador primario ─────────────────
            $table->id('cliente_id');

            // ─── Datos personales ──────────────────────
            $table->string('cliente_nombre', 100);
            $table->string('cliente_apellido', 100);
            $table->string('cliente_dni', 19)->unique();
            $table->string('cliente_estado_civil', 50)->nullable();
            $table->string('cliente_telefono', 29)->nullable();
            $table->string('cliente_direccion', 200)->nullable();

            // ─── Datos familiares ──────────────────────
            $table->integer('cliente_numero_hijos')->nullable()->default(0);
            $table->string('cliente_nombres_hijos', 250)->nullable();

            // ─── Datos laborales ───────────────────────
            $table->string('cliente_profesion', 200)->nullable();
            $table->string('cliente_lugar_trabajo', 350)->nullable();
            $table->string('cliente_direccion_trabajo', 350)->nullable();
            $table->string('cliente_telefono_trabajo', 29)->nullable();
            $table->decimal('cliente_salario_mensual', 20, 2)->nullable();

            // ─── Estado del registro ───────────────────
            $table->string('cliente_estado', 25)->default('activo');

            // ─── Timestamps ────────────────────────────
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
