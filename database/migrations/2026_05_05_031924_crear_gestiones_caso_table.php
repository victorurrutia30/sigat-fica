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
        Schema::create('gestiones_caso', function (Blueprint $table) {
            $table->id();

            $table->foreignId('caso_seguimiento_id')
                ->constrained('casos_seguimiento')
                ->cascadeOnDelete();

            $table->foreignId('registrado_por')
                ->constrained('users')
                ->restrictOnDelete();

            $table->date('fecha_gestion');

            $table->enum('medio_contacto', [
                'llamada',
                'correo',
                'presencial',
                'whatsapp',
                'otro',
            ]);

            $table->text('accion_realizada');
            $table->text('resultado')->nullable();

            $table->timestamps();

            $table->index(['caso_seguimiento_id', 'fecha_gestion'], 'gestiones_caso_fecha_idx');
            $table->index('registrado_por', 'gestiones_registrado_por_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gestiones_caso');
    }
};
