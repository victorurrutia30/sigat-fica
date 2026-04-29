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
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'nombre');
            $table->renameColumn('email', 'correo');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('rol', ['coordinacion', 'tutor'])
                ->after('password');

            $table->boolean('activo')
                ->default(true)
                ->after('rol');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rol', 'activo']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('correo', 'email');
            $table->renameColumn('nombre', 'name');
        });
    }
};
