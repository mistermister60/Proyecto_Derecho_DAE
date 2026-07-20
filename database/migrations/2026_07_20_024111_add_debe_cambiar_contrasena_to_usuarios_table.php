<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Añade el campo 'debe_cambiar_contrasena' (boolean) a la tabla usuarios.
     * Cuando es true, el usuario será forzado a cambiar su contraseña en el
     * siguiente inicio de sesión (flujo de primer login para Procuradores).
     */
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->boolean('debe_cambiar_contrasena')->default(false)->after('contrasena');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('debe_cambiar_contrasena');
        });
    }
};
