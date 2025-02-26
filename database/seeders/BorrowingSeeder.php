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

        // Ambil ID Users yang bukan admin atau penjaga berdasarkan role_id
        $user_ids = DB::table('users')
            ->whereNotIn('role_id', [2,3]) // 3 = Penjaga, 4 = Admin
            ->pluck('id')
            ->toArray();

        // Ambil ID Books
        $book_ids = DB::table('books')->pluck('id')->toArray();

        // Ambil ID Approved By (hanya admin atau penjaga yang dapat menyetujui)
        $approved_by_ids = DB::table('users')
            ->whereIn('role_id', [2, 3])
            ->pluck('id')
            ->toArray();

        // Jika tidak ada data user yang valid atau book, hentikan seeding
        if (empty($user_ids) || empty($book_ids) || empty($approved_by_ids)) {
            echo "Tidak ada data user yang valid atau buku di database!\n";
            return;
        }

        $statuses = ['pending', 'approved', 'returned', 'overdue'];
        // Insert 300 Data Borrowing
        $borrowings = [];
        for ($i = 1; $i <= 300; $i++) {
            $status = $faker->randomElement($statuses);
            $created_at = $faker->dateTimeBetween('-1 year', 'now');
            $return_date = (clone $created_at)->modify('+14 days');

            $borrowings[] = [
                'id' => Str::uuid(),
                'user_id' => $faker->randomElement($user_ids),
                'book_id' => $faker->randomElement($book_ids),
                'approved_by' => $faker->randomElement($approved_by_ids),
                'status' => $status,
                'created_at' => $created_at,
                'updated_at' => $created_at,
                'return_date' => $return_date,
            ];
        }

        DB::table('borrowings')->insert($borrowings);
        echo "300 Borrowing data berhasil ditambahkan!\n";
    }
}
