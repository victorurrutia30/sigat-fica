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
        Schema::create('nominas_seccion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('seccion_id')
                ->constrained('secciones')
                ->cascadeOnDelete();

            $table->foreignId('estudiante_id')
                ->constrained('estudiantes')
                ->restrictOnDelete();

            $table->date('fecha_registro')->nullable();

            $table->timestamps();

            $table->unique(['seccion_id', 'estudiante_id'], 'nominas_seccion_estudiante_unique');
            $table->index('estudiante_id', 'nominas_estudiante_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nominas_seccion');
    }
};
