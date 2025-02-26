<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = Carbon::now();
        $faker = Faker::create();

        // Data user yang sudah ada
        $users = [
            [
                'id' => Str::uuid(),
                'username' => 'RegularUser',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role_id' => 1, // Regular user
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => Str::uuid(),
                'username' => 'PenjagaUser',
                'email' => 'penjaga@example.com',
                'password' => Hash::make('password'),
                'role_id' => 2, // Penjaga
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => Str::uuid(),
                'username' => 'AdminUser',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role_id' => 3, // Admin
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ];

        for ($i = 0; $i < 20; $i++) {
            $users[] = [
                'id' => Str::uuid(),
                'username' => $faker->userName,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'role_id' => $faker->randomElement([1]),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        DB::table('users')->insert($users);
        echo "User seeding berhasil dengan tambahan 20 user baru!\n";
    }
}
