<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BorrowingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user_id = DB::table('users')->where('username', 'RegularUser')->value('id');
        $book_id = DB::table('books')->where('title', 'Seeder Buku 2')->value('id');
        $approved_by = DB::table('users')->where('username', 'PenjagaUser')->value('id');
        DB::table('borrowings')->insert([
            [
                'id' => Str::uuid(),
                'user_id' => $user_id,
                'book_id' => $book_id,
                'approved_by' => $approved_by,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $user_id,
                'book_id' => $book_id,
                'approved_by' => $approved_by,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
