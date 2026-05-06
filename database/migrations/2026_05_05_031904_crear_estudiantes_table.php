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
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('carne', 20)->unique();
            $table->string('nombre_completo', 200);
            $table->string('correo', 191)->nullable();
            $table->string('carrera', 150)->nullable();
            $table->timestamps();

            $table->index('nombre_completo', 'estudiantes_nombre_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
