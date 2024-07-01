<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insira os status na tabela statuses
        DB::table('users')->insert([
            ['name' => 'eSocial','email' => 'esocial@teste', 'password' => '$2y$12$Ovwo3fXiLYIwcaAcsmyuC.vCTWTmlF5xmDkkdFrQcP0hW6EyIdVeK', 'role' => 'admin','created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
