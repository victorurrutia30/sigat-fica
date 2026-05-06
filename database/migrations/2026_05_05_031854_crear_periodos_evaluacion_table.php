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
        Schema::create('periodos_evaluacion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ciclo_id')
                ->constrained('ciclos')
                ->restrictOnDelete();

            $table->string('nombre', 100);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->date('fecha_limite_consolidado');
            $table->boolean('activo')->default(false);

            $table->timestamps();

            $table->unique(['ciclo_id', 'nombre'], 'periodos_ciclo_nombre_unique');
            $table->index(['ciclo_id', 'activo'], 'periodos_ciclo_activo_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodos_evaluacion');
    }
};
