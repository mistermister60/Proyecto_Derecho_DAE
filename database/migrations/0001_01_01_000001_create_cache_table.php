<?php

/**
 * ═══════════════════════════════════════════════════════
 * MIGRACIÓN: cache / cache_locks
 * ═══════════════════════════════════════════════════════
 * Crea las tablas 'cache' y 'cache_locks' del sistema de
 * caché de Laravel. Almacena valores cacheados con expiración
 * y gestiona bloqueos atómicos para evitar condiciones de
 * carrera en operaciones de caché concurrentes.
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
        // ─── Creación de tabla: cache ──────────────────
        // Almacena pares clave-valor con timestamp de expiración
        // Columnas: key (PK), value, expiration
        Schema::create('cache', function (Blueprint $table) {
            // ─── Clave primaria (string) ─────────────────
            $table->string('key')->primary();

            // ─── Valor cacheado ─────────────────────────
            $table->mediumText('value');

            // ─── Expiración (timestamp Unix) ────────────
            $table->bigInteger('expiration')->index();
        });

        // ─── Creación de tabla: cache_locks ────────────
        // Gestiona bloqueos atómicos para operaciones de caché
        // Columnas: key (PK), owner, expiration
        Schema::create('cache_locks', function (Blueprint $table) {
            // ─── Clave primaria (string) ─────────────────
            $table->string('key')->primary();

            // ─── Propietario del bloqueo ────────────────
            $table->string('owner');

            // ─── Expiración del bloqueo ─────────────────
            $table->bigInteger('expiration')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
