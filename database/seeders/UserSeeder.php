<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = Carbon::now();

        DB::table('users')->insert([
            [
                'id' => Str::uuid(),
                'username' => 'AdminUser',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role_id' => 4, // Assuming 4 is the ID for admin
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => Str::uuid(),
                'username' => 'RegularUser',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role_id' => 1, // Assuming 1 is the ID for user
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => Str::uuid(),
                'username' => 'AuthorUser',
                'email' => 'author@example.com',
                'password' => Hash::make('password'),
                'role_id' => 2, // Assuming 2 is the ID for author
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => Str::uuid(),
                'username' => 'PenjagaUser',
                'email' => 'penjaga@example.com',
                'password' => Hash::make('password'),
                'role_id' => 3, // Assuming 3 is the ID for penjaga
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ]);
    }
}
