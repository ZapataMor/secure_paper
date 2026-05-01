<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UsersWithMembershipSeeder extends Seeder
{
    public function run(): void
    {
        $clientRoleId = DB::table('roles')->where('name', 'client')->value('id');

        if (! $clientRoleId) {
            throw new RuntimeException('No se encontro el rol client para crear usuarios con membresia.');
        }

        $now = now();
        $password = Hash::make('SecurePapers123*');

        $users = [
            [
                'role_id' => $clientRoleId,
                'name' => 'Laura',
                'last_name' => 'Esencial',
                'email' => 'cliente-plan-esencial@securepapers.test',
                'phone' => '3000000101',
                'document_type' => 'CC',
                'document_number' => '1000000101',
                'password' => $password,
                'profile_photo' => null,
                'status' => 'active',
                'email_verified_at' => $now,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'role_id' => $clientRoleId,
                'name' => 'Diego',
                'last_name' => 'Completa',
                'email' => 'cliente-plan-completa@securepapers.test',
                'phone' => '3000000102',
                'document_type' => 'CC',
                'document_number' => '1000000102',
                'password' => $password,
                'profile_photo' => null,
                'status' => 'active',
                'email_verified_at' => $now,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'role_id' => $clientRoleId,
                'name' => 'Valentina',
                'last_name' => 'Premium',
                'email' => 'cliente-plan-premium@securepapers.test',
                'phone' => '3000000103',
                'document_type' => 'CC',
                'document_number' => '1000000103',
                'password' => $password,
                'profile_photo' => null,
                'status' => 'active',
                'email_verified_at' => $now,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
        ];

        DB::table('users')->upsert(
            $users,
            ['email'],
            [
                'role_id',
                'name',
                'last_name',
                'phone',
                'document_type',
                'document_number',
                'password',
                'profile_photo',
                'status',
                'email_verified_at',
                'remember_token',
                'updated_at',
                'deleted_at',
            ]
        );

        $planNamesByEmail = [
            'cliente-plan-esencial@securepapers.test' => 'Esencial',
            'cliente-plan-completa@securepapers.test' => 'Correccion Completa',
            'cliente-plan-premium@securepapers.test' => 'Premium',
        ];

        $plans = DB::table('payment_plans')
            ->whereIn('name', array_values($planNamesByEmail))
            ->get(['id', 'name', 'price', 'duration_days'])
            ->keyBy('name');

        if ($plans->count() !== 3) {
            throw new RuntimeException('No se encontraron los 3 planes requeridos para asignar membresias.');
        }

        foreach ($planNamesByEmail as $email => $planName) {
            $userId = DB::table('users')->where('email', $email)->value('id');
            $plan = $plans->get($planName);

            if (! $userId || ! $plan) {
                continue;
            }

            $startDate = Carbon::today();
            $endDate = $startDate->copy()->addDays((int) $plan->duration_days);

            DB::table('user_subscriptions')->updateOrInsert(
                [
                    'user_id' => $userId,
                    'payment_plan_id' => $plan->id,
                ],
                [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'status' => 'active',
                    'payment_status' => 'paid',
                    'amount_paid' => $plan->price,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
