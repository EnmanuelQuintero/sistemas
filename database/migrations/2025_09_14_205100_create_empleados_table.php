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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('foto')->nullable();
            $table->string('nombre');
            $table->string('cedula')->unique();
            $table->date('ingreso');
            $table->string('inss');
            $table->unsignedBigInteger('id_area');
            $table->string('cargo');
            $table->decimal('salario', 8, 2);
            $table->date('finalizo')->nullable();
            $table->timestamps();

            $table->foreign('id_area')->references('id')->on('areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
