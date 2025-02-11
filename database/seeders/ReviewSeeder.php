<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $user_id = DB::table('users')->where('username', 'RegularUser')->value('id');
        $book_id = DB::table('books')->where('title', 'Seeder Buku 2')->value('id');
        DB::table('reviews')->insert([
            [
                'id' => Str::uuid(),
                'user_id' => $user_id,
                'book_id' => $book_id,
                'rating' => 5,
                'comment' => 'Sangat Menakjubkan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $user_id,
                'book_id' => $book_id,
                'rating' => 4,
                'comment' => 'keren banget',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
