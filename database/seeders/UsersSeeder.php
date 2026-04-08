<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $clientRoleId = DB::table('roles')->where('name', 'client')->value('id');

        if (! $adminRoleId || ! $clientRoleId) {
            throw new RuntimeException('No se encontraron los roles requeridos: admin y client.');
        }

        $now = now();
        $password = Hash::make('SecurePapers123*');

        DB::table('users')->upsert(
            [
                [
                    'role_id' => $adminRoleId,
                    'name' => 'Carlos',
                    'last_name' => 'Administrador',
                    'email' => 'admin@securepapers.test',
                    'phone' => '3000000001',
                    'document_type' => 'CC',
                    'document_number' => '1000000001',
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
                    'name' => 'Ana',
                    'last_name' => 'Gomez',
                    'email' => 'cliente1@securepapers.test',
                    'phone' => '3000000002',
                    'document_type' => 'CC',
                    'document_number' => '1000000002',
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
                    'name' => 'Luis',
                    'last_name' => 'Ramirez',
                    'email' => 'cliente2@securepapers.test',
                    'phone' => '3000000003',
                    'document_type' => 'CC',
                    'document_number' => '1000000003',
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
                    'name' => 'Maria',
                    'last_name' => 'Torres',
                    'email' => 'cliente3@securepapers.test',
                    'phone' => '3000000004',
                    'document_type' => 'CC',
                    'document_number' => '1000000004',
                    'password' => $password,
                    'profile_photo' => null,
                    'status' => 'active',
                    'email_verified_at' => $now,
                    'remember_token' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ],
            ],
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
    }
}

