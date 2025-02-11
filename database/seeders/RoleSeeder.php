<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'user'],
            ['name' => 'author'],
            ['name' => 'penjaga'],
            ['name' => 'admin'],
        ]);
    }
}
