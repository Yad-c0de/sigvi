<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 255);
            $table->text('descripcion_tecnica')->nullable();
            $table->string('foto', 255)->nullable();

            // Relaciones
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('marca_id')->constrained('marcas');
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();

            // Precios e Inventario
            $table->decimal('precio_costo', 10, 2)->unsigned()->default(0);
            $table->decimal('precio_venta', 10, 2)->unsigned();
            $table->integer('stock')->unsigned()->default(0);
            $table->integer('stock_minimo')->unsigned()->default(5);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
