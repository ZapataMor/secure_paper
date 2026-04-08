<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('roles')->upsert(
            [
                [
                    'name' => 'admin',
                    'description' => 'Administrador del sistema',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'client',
                    'description' => 'Cliente del sistema',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            ['name'],
            ['description', 'updated_at']
        );
    }
}

