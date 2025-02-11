<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = Carbon::now();

        DB::table('categories')->insert([
            ['name' => 'science', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'horror', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'action', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ]);
    }
}
