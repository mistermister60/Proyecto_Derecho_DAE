<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Elimina la columna es_superadmin de roles (código muerto: no usada en la app).
     */
    public function up(): void
    {
        if (Schema::hasColumn('roles', 'es_superadmin')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('es_superadmin');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('roles', 'es_superadmin')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->boolean('es_superadmin')->default(false);
            });
        }
    }
};
