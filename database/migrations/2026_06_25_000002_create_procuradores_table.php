<?php

/**
 * ═══════════════════════════════════════════════════════
 * MIGRACIÓN: procuradores
 * ═══════════════════════════════════════════════════════
 * Crea la tabla 'procuradores' que almacena los datos
 * personales y profesionales de los procuradores (abogados)
 * del consultorio jurídico. Un procurador puede tener un
 * usuario asociado en el sistema para iniciar sesión.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Creación de tabla: procuradores ───────────
        // Columnas: procurador_id, nombre, apellido, dni, carnet,
        //           fecha_nacimiento, genero, email, telefono, direccion, estado, timestamps
        Schema::create('procuradores', function (Blueprint $table) {
            // ─── Identificador primario ─────────────────
            $table->id('procurador_id');

            // ─── Datos personales ──────────────────────
            $table->string('procurador_nombre', 100);
            $table->string('procurador_apellido', 100);
            $table->string('procurador_dni', 19)->unique();
            $table->string('procurador_carnet', 20)->unique()->nullable();
            $table->date('procurador_fecha_nacimiento');
            $table->string('procurador_genero', 25);

            // ─── Datos de contacto ─────────────────────
            $table->string('procurador_email', 150)->unique();
            $table->string('procurador_telefono', 29)->nullable();
            $table->string('procurador_direccion', 200)->nullable();

            // ─── Estado del registro ───────────────────
            $table->string('procurador_estado', 25)->default('activo');

            // ─── Timestamps ────────────────────────────
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procuradores');
    }
};
