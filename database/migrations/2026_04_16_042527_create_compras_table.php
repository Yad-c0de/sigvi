<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->string('comprobante_numero', 50);
            $table->decimal('total', 10, 2)->unsigned();
            $table->date('fecha_compra');
            $table->timestamps();
            $table->index('comprobante_numero');
            $table->index('fecha_compra');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
