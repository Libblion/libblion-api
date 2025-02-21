<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class BorrowingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Ambil ID Users, Books, dan Approved By
        $user_ids = DB::table('users')->pluck('id')->toArray();
        $book_ids = DB::table('books')->pluck('id')->toArray();
        $approved_by_ids = DB::table('users')->pluck('id')->toArray(); // Bisa diubah ke role tertentu

        // Jika tidak ada data user atau book, hentikan seeding
        if (empty($user_ids) || empty($book_ids) || empty($approved_by_ids)) {
            echo "Tidak ada data user atau buku di database!\n";
            return;
        }

        // Insert 10 Data Borrowing
        $borrowings = [];
        for ($i = 1; $i <= 100; $i++) {
            $borrowings[] = [
                'id' => Str::uuid(),
                'user_id' => $faker->randomElement($user_ids),
                'book_id' => $faker->randomElement($book_ids),
                'approved_by' => $faker->randomElement($approved_by_ids),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('borrowings')->insert($borrowings);
        echo "10 Borrowing data berhasil ditambahkan!\n";
    }
}
