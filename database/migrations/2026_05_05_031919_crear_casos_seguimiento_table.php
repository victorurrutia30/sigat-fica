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
        Schema::create('casos_seguimiento', function (Blueprint $table) {
            $table->id();

            $table->foreignId('periodo_evaluacion_id')
                ->constrained('periodos_evaluacion')
                ->restrictOnDelete();

            $table->foreignId('seccion_id')
                ->constrained('secciones')
                ->restrictOnDelete();

            $table->foreignId('estudiante_id')
                ->constrained('estudiantes')
                ->restrictOnDelete();

            $table->foreignId('tutor_id')
                ->constrained('tutores')
                ->restrictOnDelete();

            $table->foreignId('causa_id')
                ->nullable()
                ->constrained('causas')
                ->restrictOnDelete();

            $table->enum('resultado_final', ['retiro', 'abandono'])->nullable();
            $table->boolean('cerrado')->default(false);
            $table->timestamp('cerrado_en')->nullable();

            $table->foreignId('registrado_por')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique(
                ['periodo_evaluacion_id', 'seccion_id', 'estudiante_id'],
                'casos_periodo_seccion_estudiante_unique'
            );

            $table->index(['tutor_id', 'cerrado'], 'casos_tutor_cerrado_idx');
            $table->index('causa_id', 'casos_causa_idx');
            $table->index('registrado_por', 'casos_registrado_por_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casos_seguimiento');
    }
};
