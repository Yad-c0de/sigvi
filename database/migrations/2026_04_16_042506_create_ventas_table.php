<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('tipo_comprobante', ['Boleta', 'Factura']);
            $table->string('serie', 4);
            $table->integer('correlativo');
            $table->decimal('subtotal', 10, 2)->unsigned();
            $table->decimal('igv', 10, 2)->unsigned();
            $table->decimal('total', 10, 2)->unsigned();
            $table->string('total_letras', 255);
            $table->string('forma_pago', 50);
            $table->enum('estado', ['Completada', 'Anulada'])->default('Completada');
            $table->timestamps();
            $table->index('estado');
            $table->unique(['serie', 'correlativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
