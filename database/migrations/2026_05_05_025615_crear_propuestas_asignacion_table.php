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
        Schema::create('propuestas_asignacion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ciclo_id')
                ->constrained('ciclos')
                ->restrictOnDelete();

            $table->foreignId('creado_por')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('enviado_en')->nullable();

            $table->enum('estado_aprobacion', [
                'pendiente',
                'aprobado',
                'requiere_ajustes',
            ])->default('pendiente');

            $table->text('observaciones_decano')->nullable();
            $table->date('fecha_respuesta_decano')->nullable();

            $table->foreignId('respuesta_registrada_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('publicado')->default(false);
            $table->unsignedTinyInteger('version')->default(1);

            $table->timestamps();

            $table->index(['ciclo_id', 'estado_aprobacion']);
            $table->index(['publicado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propuestas_asignacion');
    }
};
