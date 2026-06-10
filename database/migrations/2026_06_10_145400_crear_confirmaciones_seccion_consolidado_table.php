<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('confirmaciones_seccion_consolidado', function (Blueprint $table) {
            $table->id();

            $table->foreignId('consolidado_id')
                ->constrained('consolidados')
                ->cascadeOnDelete();

            $table->foreignId('seccion_id')
                ->constrained('secciones')
                ->restrictOnDelete();

            $table->foreignId('confirmado_por')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('confirmado_en');
            $table->timestamps();

            $table->unique(
                ['consolidado_id', 'seccion_id'],
                'confirmaciones_consolidado_seccion_unique'
            );

            $table->index('seccion_id', 'confirmaciones_seccion_idx');
            $table->index('confirmado_por', 'confirmaciones_confirmado_por_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('confirmaciones_seccion_consolidado');
    }
};
