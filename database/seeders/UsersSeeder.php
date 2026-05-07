<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $password = Hash::make('SecurePapers123*');

        DB::table('users')->upsert(
            [
                [
                    'role' => 'admin',
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
                    'role' => 'client',
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
            ],
            ['email'],
            [
                'role',
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
