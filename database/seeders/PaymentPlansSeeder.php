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
                    'name' => 'Paquete Asesoría Esencial',
                    'description' => 'Servicio de entrada para ordenar ideas y recibir feedback experto con Inteligencia Híbrida.',
                    'price' => 53.55,
                    'duration_days' => 30,
                    'max_documents' => 3,
                    'includes_meetings' => true,
                    'meetings_limit' => 1,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Paquete Corrección Completa',
                    'description' => 'Corrección profunda de manuscritos académicos en Ciencias Sociales y Educación.',
                    'price' => 238,
                    'duration_days' => 30,
                    'max_documents' => 10,
                    'includes_meetings' => true,
                    'meetings_limit' => 2,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Paquete Premium Publicación Científica',
                    'description' => 'Acompañamiento integral hasta publicación científica con soporte por un año.',
                    'price' => 1071,
                    'duration_days' => 365,
                    'max_documents' => 25,
                    'includes_meetings' => true,
                    'meetings_limit' => 5,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Paquete Segundo Tutor',
                    'description' => 'Membresía trimestral de acompañamiento continuo para pregrado, posgrado y doctorado.',
                    'price' => 991,
                    'duration_days' => 90,
                    'max_documents' => 12,
                    'includes_meetings' => true,
                    'meetings_limit' => 4,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Paquete Redacción Express Profesional',
                    'description' => 'Redacción rápida para clientes con material completo y fechas límite ajustadas.',
                    'price' => 238,
                    'duration_days' => 30,
                    'max_documents' => 10,
                    'includes_meetings' => true,
                    'meetings_limit' => 2,
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
