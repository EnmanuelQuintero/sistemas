<?php

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
        Schema::create('detallenominas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nomina_id');
            $table->unsignedBigInteger('empleado_id');
            $table->decimal('inss', 8, 2);
            $table->decimal('ir', 8, 2);
            $table->decimal('inatec', 8, 2);
            $table->decimal('patronal', 8, 2);
            $table->timestamps();

            $table->foreign('nomina_id')->references('id')->on('nominas')->onDelete('cascade');
            $table->foreign('empleado_id')->references('id')->on('empleados')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detallenominas');
    }
};
