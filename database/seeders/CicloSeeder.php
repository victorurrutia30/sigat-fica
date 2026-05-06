<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CicloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ahora = now();

        // Solo debe haber un ciclo activo. Esta regla se termina de validar en aplicación.
        DB::table('ciclos')->update([
            'activo' => false,
            'updated_at' => $ahora,
        ]);

        DB::table('ciclos')->updateOrInsert(
            ['nombre' => '2026-01'],
            [
                'anio' => 2026,
                'periodo' => 1,
                'fecha_inicio' => '2026-01-15',
                'fecha_fin' => '2026-06-15',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]
        );
    }
}
