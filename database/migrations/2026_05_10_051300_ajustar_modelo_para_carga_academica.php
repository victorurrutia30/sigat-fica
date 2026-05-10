<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->boolean('gestionada_por_coordinacion')
                ->default(false)
                ->after('departamento');

            $table->boolean('requiere_revision')
                ->default(false)
                ->after('gestionada_por_coordinacion');
        });

        DB::statement('ALTER TABLE materias MODIFY ciclo_plan TINYINT UNSIGNED NULL');

        DB::statement(
            "ALTER TABLE secciones MODIFY modalidad ENUM('presencial', 'virtual', 'mixta', 'en_linea') NOT NULL"
        );

        Schema::table('secciones', function (Blueprint $table) {
            $table->boolean('requiere_tutor')
                ->default(true)
                ->after('modalidad');

            $table->string('codigo_docente_titular', 30)
                ->nullable()
                ->after('correo_titular');

            $table->string('categoria_docente_titular', 30)
                ->nullable()
                ->after('codigo_docente_titular');

            $table->text('observaciones_carga')
                ->nullable()
                ->after('capacidad');
        });

        Schema::create('importaciones_carga_academica', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ciclo_id')
                ->constrained('ciclos')
                ->restrictOnDelete();

            $table->foreignId('usuario_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('nombre_archivo', 255);
            $table->string('hash_archivo', 64)->nullable();

            $table->unsignedInteger('hojas_procesadas')->default(0);
            $table->unsignedInteger('filas_leidas')->default(0);
            $table->unsignedInteger('filas_importadas')->default(0);
            $table->unsignedInteger('filas_ignoradas')->default(0);
            $table->unsignedInteger('filas_error')->default(0);

            $table->unsignedInteger('materias_creadas')->default(0);
            $table->unsignedInteger('materias_actualizadas')->default(0);
            $table->unsignedInteger('secciones_creadas')->default(0);
            $table->unsignedInteger('secciones_actualizadas')->default(0);
            $table->unsignedInteger('horarios_creados')->default(0);

            $table->enum('estado', [
                'procesado',
                'procesado_con_observaciones',
                'fallido',
            ])->default('procesado');

            $table->json('resumen_json')->nullable();
            $table->json('errores_json')->nullable();

            $table->timestamps();

            $table->index(['ciclo_id', 'created_at'], 'import_carga_ciclo_fecha_idx');
            $table->index(['estado'], 'import_carga_estado_idx');
            $table->index(['hash_archivo'], 'import_carga_hash_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('importaciones_carga_academica');

        Schema::table('secciones', function (Blueprint $table) {
            $table->dropColumn([
                'requiere_tutor',
                'codigo_docente_titular',
                'categoria_docente_titular',
                'observaciones_carga',
            ]);
        });

        DB::statement("UPDATE secciones SET modalidad = 'mixta' WHERE modalidad = 'en_linea'");

        DB::statement(
            "ALTER TABLE secciones MODIFY modalidad ENUM('presencial', 'virtual', 'mixta') NOT NULL"
        );

        DB::statement('UPDATE materias SET ciclo_plan = 1 WHERE ciclo_plan IS NULL');
        DB::statement('ALTER TABLE materias MODIFY ciclo_plan TINYINT UNSIGNED NOT NULL');

        Schema::table('materias', function (Blueprint $table) {
            $table->dropColumn([
                'gestionada_por_coordinacion',
                'requiere_revision',
            ]);
        });
    }
};
