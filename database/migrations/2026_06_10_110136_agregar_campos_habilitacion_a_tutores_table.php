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
        Schema::table('tutores', function (Blueprint $table) {
            $table->string('categoria_docente', 30)
                ->nullable()
                ->after('departamento');

            $table->boolean('habilitado_para_tutorias')
                ->default(true)
                ->after('tiempo_completo');

            $table->boolean('es_excepcion_tutoria')
                ->default(false)
                ->after('habilitado_para_tutorias');

            $table->text('motivo_excepcion_tutoria')
                ->nullable()
                ->after('es_excepcion_tutoria');

            $table->string('origen_registro', 30)
                ->default('manual')
                ->after('motivo_excepcion_tutoria');

            $table->index([
                'activo',
                'habilitado_para_tutorias',
                'tiempo_completo',
                'es_excepcion_tutoria',
            ], 'idx_tutores_habilitacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tutores', function (Blueprint $table) {
            $table->dropIndex('idx_tutores_habilitacion');

            $table->dropColumn([
                'categoria_docente',
                'habilitado_para_tutorias',
                'es_excepcion_tutoria',
                'motivo_excepcion_tutoria',
                'origen_registro',
            ]);
        });
    }
};
