<?php

/**
 * ═══════════════════════════════════════════════════════
 * MIGRACIÓN: jobs / job_batches / failed_jobs
 * ═══════════════════════════════════════════════════════
 * Crea las tablas del sistema de colas (queues) de Laravel.
 * 'jobs' almacena trabajos pendientes, 'job_batches' agrupa
 * lotes de trabajos, y 'failed_jobs' registra aquellos que
 * fallaron para su posterior depuración y reintento.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ─── Creación de tabla: jobs ───────────────────
        // Cola de trabajos pendientes por procesar
        // Columnas: id, queue, payload, attempts, reserved_at, available_at, created_at
        Schema::create('jobs', function (Blueprint $table) {
            // ─── Identificador primario ─────────────────
            $table->id();

            // ─── Nombre de la cola ─────────────────────
            $table->string('queue')->index();

            // ─── Datos serializados del trabajo ─────────
            $table->longText('payload');

            // ─── Número de intentos realizados ─────────
            $table->unsignedSmallInteger('attempts');

            // ─── Timestamp de reserva (bloqueo temporal) ─
            $table->unsignedInteger('reserved_at')->nullable();

            // ─── Disponible a partir de (timestamp) ────
            $table->unsignedInteger('available_at');

            // ─── Fecha de creación (timestamp) ─────────
            $table->unsignedInteger('created_at');
        });

        // ─── Creación de tabla: job_batches ────────────
        // Agrupa trabajos en lotes para procesamiento por lotes
        // Columnas: id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, timestamps
        Schema::create('job_batches', function (Blueprint $table) {
            // ─── Identificador único del lote ──────────
            $table->string('id')->primary();

            // ─── Nombre descriptivo del lote ───────────
            $table->string('name');

            // ─── Total de trabajos en el lote ──────────
            $table->integer('total_jobs');

            // ─── Trabajos pendientes ───────────────────
            $table->integer('pending_jobs');

            // ─── Trabajos fallidos ────────────────────
            $table->integer('failed_jobs');

            // ─── IDs de trabajos fallidos (serializado) ─
            $table->longText('failed_job_ids');

            // ─── Opciones adicionales (serializado) ────
            $table->mediumText('options')->nullable();

            // ─── Timestamp de cancelación ─────────────
            $table->integer('cancelled_at')->nullable();

            // ─── Timestamp de creación ────────────────
            $table->integer('created_at');

            // ─── Timestamp de finalización ────────────
            $table->integer('finished_at')->nullable();
        });

        // ─── Creación de tabla: failed_jobs ────────────
        // Registro de trabajos que fallaron para depuración
        // Columnas: id, uuid, connection, queue, payload, exception, failed_at
        Schema::create('failed_jobs', function (Blueprint $table) {
            // ─── Identificador primario ─────────────────
            $table->id();

            // ─── UUID único del trabajo fallido ────────
            $table->string('uuid')->unique();

            // ─── Nombre de la conexión de cola ─────────
            $table->string('connection');

            // ─── Nombre de la cola ─────────────────────
            $table->string('queue');

            // ─── Datos serializados del trabajo ─────────
            $table->longText('payload');

            // ─── Excepción serializada ─────────────────
            $table->longText('exception');

            // ─── Fecha/hora del fallo ──────────────────
            $table->timestamp('failed_at')->useCurrent();

            // ─── Índice compuesto para búsquedas ───────
            $table->index(['connection', 'queue', 'failed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
