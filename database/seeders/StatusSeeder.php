<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insira os status na tabela statuses
        DB::table('status')->insert([
            ['name' => 'Não iniciado', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Parado', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Em andamento', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Finalizado', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
