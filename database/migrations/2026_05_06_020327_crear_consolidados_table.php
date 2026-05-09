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
        Schema::create('consolidados', function (Blueprint $table) {
            $table->id();

            $table->foreignId('periodo_evaluacion_id')
                ->constrained('periodos_evaluacion')
                ->restrictOnDelete();

            $table->foreignId('tutor_id')
                ->constrained('tutores')
                ->restrictOnDelete();

            $table->enum('estado_entrega', [
                'pendiente',
                'entregado',
                'con_observaciones',
            ])->default('pendiente');

            $table->boolean('sin_casos')->default(false);
            $table->timestamp('entregado_en')->nullable();

            $table->foreignId('entregado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('observaciones_coord')->nullable();

            $table->foreignId('revisado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('revisado_en')->nullable();

            $table->timestamps();

            $table->unique(
                ['periodo_evaluacion_id', 'tutor_id'],
                'consolidados_periodo_tutor_unique'
            );

            $table->index(['estado_entrega', 'sin_casos'], 'consolidados_estado_sin_casos_idx');
            $table->index('entregado_por', 'consolidados_entregado_por_idx');
            $table->index('revisado_por', 'consolidados_revisado_por_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consolidados');
    }
};
