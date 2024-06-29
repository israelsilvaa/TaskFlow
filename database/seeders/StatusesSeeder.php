<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insira os status na tabela statuses
        DB::table('statuses')->insert([
            ['name' => 'Não iniciado','classe' => 'secondary', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Parado','classe' => 'danger', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Em andamento','classe' => 'info', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Finalizado','classe' => 'success', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
