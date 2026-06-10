<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('casos_seguimiento', 'detalle_inasistencia')) {
            Schema::table('casos_seguimiento', function (Blueprint $table) {
                $table->text('detalle_inasistencia')
                    ->nullable()
                    ->after('resultado_final');
            });
        }

        if (! Schema::hasColumn('casos_seguimiento', 'resultado_consolidado')) {
            Schema::table('casos_seguimiento', function (Blueprint $table) {
                $table->enum('resultado_consolidado', [
                    'rc',
                    'rm',
                    'abm',
                    'abc',
                ])
                    ->nullable()
                    ->after('detalle_inasistencia');
            });
        }

        if (! Schema::hasColumn('casos_seguimiento', 'matricula')) {
            Schema::table('casos_seguimiento', function (Blueprint $table) {
                $table->boolean('matricula')
                    ->nullable()
                    ->after('resultado_consolidado');
            });
        }

        if (! Schema::hasColumn('casos_seguimiento', 'cuota_cancelada')) {
            Schema::table('casos_seguimiento', function (Blueprint $table) {
                $table->string('cuota_cancelada', 50)
                    ->nullable()
                    ->after('matricula');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('casos_seguimiento', 'cuota_cancelada')) {
            Schema::table('casos_seguimiento', function (Blueprint $table) {
                $table->dropColumn('cuota_cancelada');
            });
        }

        if (Schema::hasColumn('casos_seguimiento', 'matricula')) {
            Schema::table('casos_seguimiento', function (Blueprint $table) {
                $table->dropColumn('matricula');
            });
        }

        if (Schema::hasColumn('casos_seguimiento', 'resultado_consolidado')) {
            Schema::table('casos_seguimiento', function (Blueprint $table) {
                $table->dropColumn('resultado_consolidado');
            });
        }

        if (Schema::hasColumn('casos_seguimiento', 'detalle_inasistencia')) {
            Schema::table('casos_seguimiento', function (Blueprint $table) {
                $table->dropColumn('detalle_inasistencia');
            });
        }
    }
};
