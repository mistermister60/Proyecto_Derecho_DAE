<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('usuario_id');
            $table->foreignId('rol_id')->constrained('roles', 'rol_id');
            $table->foreignId('procurador_id')->nullable()->constrained('procuradores', 'procurador_id');
            $table->string('usuario_nombre');
            $table->string('email')->unique();
            $table->string('contrasena');
            $table->string('usuario_estado', 25)->default('activo');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('restablecimiento_contrasenas', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sesiones', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('usuario_id')->nullable()->index()->constrained('usuarios', 'usuario_id')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
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
