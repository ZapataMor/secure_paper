<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentPlansSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('payment_plans')->upsert(
            [
                [
                    'name' => 'Esencial',
                    'description' => 'Idea inicial, tesis en etapa temprana o consulta rapida.',
                    'price' => 49,
                    'duration_days' => 30,
                    'max_documents' => 3,
                    'includes_meetings' => true,
                    'meetings_limit' => 1,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Correccion Completa',
                    'description' => 'Correccion profunda de tesis o articulo.',
                    'price' => 299,
                    'duration_days' => 30,
                    'max_documents' => 10,
                    'includes_meetings' => true,
                    'meetings_limit' => 2,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Premium',
                    'description' => 'Acompanamiento completo hasta publicacion.',
                    'price' => 999,
                    'duration_days' => 60,
                    'max_documents' => 25,
                    'includes_meetings' => true,
                    'meetings_limit' => 5,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            ['name'],
            [
                'description',
                'price',
                'duration_days',
                'max_documents',
                'includes_meetings',
                'meetings_limit',
                'status',
                'updated_at',
            ]
        );
    }
}
