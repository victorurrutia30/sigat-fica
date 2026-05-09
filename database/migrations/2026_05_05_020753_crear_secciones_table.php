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
        Schema::create('secciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ciclo_id')
                ->constrained('ciclos')
                ->restrictOnDelete();

            $table->foreignId('materia_id')
                ->constrained('materias')
                ->restrictOnDelete();

            $table->string('numero_seccion', 10);
            $table->enum('modalidad', ['presencial', 'virtual', 'mixta']);
            $table->string('aula', 60)->nullable();
            $table->string('nombre_titular', 200);
            $table->string('correo_titular', 191)->nullable();
            $table->unsignedSmallInteger('capacidad')->default(35);
            $table->timestamps();

            $table->unique(['ciclo_id', 'materia_id', 'numero_seccion'], 'secciones_ciclo_materia_numero_unique');
            $table->index(['ciclo_id', 'materia_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secciones');
    }
};
