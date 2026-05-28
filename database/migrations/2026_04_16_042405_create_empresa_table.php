<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 11)->unique();
            $table->string('razon_social', 255);
            $table->string('direccion', 255);
            $table->string('distrito', 100);
            $table->string('ciudad', 100)->default('Trujillo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa');
    }
};
