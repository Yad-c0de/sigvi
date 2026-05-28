<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Eliminar columna direccion si existe
            if (Schema::hasColumn('clientes', 'direccion')) {
                $table->dropColumn('direccion');
            }
            // Agregar email (opcional)
            $table->string('email', 255)->nullable()->after('telefono');
            // Agregar estado (Activo/Inactivo)
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['email', 'estado']);
            $table->string('direccion', 255)->nullable();
        });
    }
};
