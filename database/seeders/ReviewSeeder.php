<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Ambil semua ID Users & Books
        $user_ids = DB::table('users')->pluck('id')->toArray();
        $book_ids = DB::table('books')->pluck('id')->toArray();

        // Pastikan ada data User & Book sebelum menjalankan seeder
        if (empty($user_ids) || empty($book_ids)) {
            echo "Tidak ada data user atau buku di database!\n";
            return;
        }

        // Insert 10 Review
        $reviews = [];
        for ($i = 1; $i <= 15; $i++) {
            $reviews[] = [
                'id' => Str::uuid(),
                'user_id' => $faker->randomElement($user_ids), // Pilih user secara acak
                'book_id' => $faker->randomElement($book_ids), // Pilih buku secara acak
                'rating' => $faker->numberBetween(1, 5), // Rating 1-5
                'comment' => $faker->sentence(6), // Komentar acak
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('reviews')->insert($reviews);
        echo "10 Review berhasil ditambahkan!\n";
    }
}
