<?php

/**
 * ═══════════════════════════════════════════════════════
 * MIGRACIÓN: usuarios / restablecimiento_contrasenas / sesiones
 * ═══════════════════════════════════════════════════════
 * Crea la tabla 'usuarios' para autenticación del sistema,
 * vinculando cada usuario a un rol y opcionalmente a un
 * procurador. Incluye tablas auxiliares para reset de
 * contraseña y gestión de sesiones (Sanctum).
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Creación de tabla: usuarios ─────────────────
        // Usuarios del sistema (Director, Procuradores)
        // Columnas: usuario_id, rol_id, procurador_id, usuario_nombre, email, contrasena, usuario_estado, remember_token, timestamps
        Schema::create('usuarios', function (Blueprint $table) {
            // ─── Identificador primario ─────────────────
            $table->id('usuario_id');

            // ─── Relaciones ──────────────────────────────
            $table->foreignId('rol_id')->constrained('roles', 'rol_id');
            $table->foreignId('procurador_id')->nullable()->constrained('procuradores', 'procurador_id');

            // ─── Datos del usuario ──────────────────────
            $table->string('usuario_nombre');
            $table->string('email')->unique();
            $table->string('contrasena');

            // ─── Estado del registro ───────────────────
            $table->string('usuario_estado', 25)->default('activo');

            // ─── Token de recordatorio ─────────────────
            $table->rememberToken();

            // ─── Timestamps ────────────────────────────
            $table->timestamps();
        });

        // ─── Creación de tabla: restablecimiento_contrasenas ─────────────────
        // Tokens temporales para restablecimiento de contraseña
        // Columnas: email (PK), token, created_at
        Schema::create('restablecimiento_contrasenas', function (Blueprint $table) {
            // ─── Email como clave primaria ──────────────
            $table->string('email')->primary();

            // ─── Token de restablecimiento ──────────────
            $table->string('token');

            // ─── Timestamp de creación ──────────────────
            $table->timestamp('created_at')->nullable();
        });

        // ─── Creación de tabla: sesiones ─────────────────
        // Gestión de sesiones de usuario (Sanctum)
        // Columnas: id (PK), user_id, ip_address, user_agent, payload, last_activity
        Schema::create('sesiones', function (Blueprint $table) {
            // ─── Identificador de sesión (PK) ───────────
            $table->string('id')->primary();

            // ─── Usuario propietario ────────────────────
            $table->foreignId('user_id')->nullable()->index()->constrained('usuarios', 'usuario_id')->onDelete('cascade');

            // ─── Información de la sesión ───────────────
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');

            // ─── Última actividad (timestamp) ───────────
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones');
        Schema::dropIfExists('restablecimiento_contrasenas');
        Schema::dropIfExists('usuarios');
    }
};
