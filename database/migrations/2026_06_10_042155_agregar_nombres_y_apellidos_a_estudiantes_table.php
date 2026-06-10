<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('estudiantes', 'nombres')) {
            Schema::table('estudiantes', function (Blueprint $table) {
                $table->string('nombres', 100)
                    ->nullable()
                    ->after('carne');
            });
        }

        if (! Schema::hasColumn('estudiantes', 'apellidos')) {
            Schema::table('estudiantes', function (Blueprint $table) {
                $table->string('apellidos', 100)
                    ->nullable()
                    ->after('nombres');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('estudiantes', 'apellidos')) {
            Schema::table('estudiantes', function (Blueprint $table) {
                $table->dropColumn('apellidos');
            });
        }

        if (Schema::hasColumn('estudiantes', 'nombres')) {
            Schema::table('estudiantes', function (Blueprint $table) {
                $table->dropColumn('nombres');
            });
        }
    }
};
