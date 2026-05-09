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
        Schema::create('tutores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_id')
                ->nullable()
                ->unique()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('codigo_empleado', 30)->unique();
            $table->string('nombre_completo', 200);
            $table->string('correo_institucional', 191)->unique();
            $table->string('departamento', 100)->nullable();
            $table->date('fecha_contratacion')->nullable();
            $table->boolean('tiempo_completo')->default(true);
            $table->boolean('activo')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['activo', 'tiempo_completo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutores');
    }
};
