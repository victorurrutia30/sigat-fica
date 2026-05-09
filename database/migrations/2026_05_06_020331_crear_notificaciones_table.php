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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('tipo', 100);
            $table->string('titulo', 200);
            $table->text('mensaje');
            $table->boolean('leido')->default(false);

            $table->string('modelo_tipo', 100)->nullable();
            $table->unsignedBigInteger('modelo_id')->nullable();

            $table->timestamp('leido_en')->nullable();

            $table->timestamps();

            $table->index(['usuario_id', 'leido'], 'notificaciones_usuario_leido_idx');
            $table->index(['modelo_tipo', 'modelo_id'], 'notificaciones_modelo_idx');
            $table->index('tipo', 'notificaciones_tipo_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
