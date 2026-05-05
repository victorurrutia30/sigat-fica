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
        Schema::create('items_propuesta', function (Blueprint $table) {
            $table->id();

            $table->foreignId('propuesta_asignacion_id')
                ->constrained('propuestas_asignacion')
                ->cascadeOnDelete();

            $table->foreignId('tutor_id')
                ->constrained('tutores')
                ->restrictOnDelete();

            $table->foreignId('seccion_id')
                ->constrained('secciones')
                ->restrictOnDelete();

            $table->boolean('prioridad')->default(false);
            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->unique(
                ['propuesta_asignacion_id', 'seccion_id'],
                'items_propuesta_propuesta_seccion_unique'
            );

            $table->index(['tutor_id', 'seccion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items_propuesta');
    }
};
