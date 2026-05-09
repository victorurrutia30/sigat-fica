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
        Schema::create('historial_cambios_propuesta', function (Blueprint $table) {
            $table->id();

            $table->foreignId('propuesta_asignacion_id')
                ->constrained('propuestas_asignacion')
                ->cascadeOnDelete();

            $table->foreignId('modificado_por')
                ->constrained('users')
                ->restrictOnDelete();

            $table->enum('tipo_cambio', [
                'item_agregado',
                'item_eliminado',
                'tutor_reemplazado',
                'estado_cambiado',
                'publicado',
                'observacion_decano',
                'ajuste_coordinacion',
            ]);

            $table->text('descripcion')->nullable();
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['propuesta_asignacion_id', 'tipo_cambio'], 'hist_prop_tipo_idx');
            $table->index('modificado_por', 'hist_prop_modificado_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_cambios_propuesta');
    }
};
