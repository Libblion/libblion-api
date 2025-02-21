<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Ambil ID Penulis dan Kategori Secara Acak
        $author_ids = DB::table('authors')->pluck('id')->toArray();
        $category_ids = DB::table('categories')->pluck('id')->toArray();

        // Jika tidak ada data di tabel authors atau categories, hentikan seeding
        if (empty($author_ids) || empty($category_ids)) {
            echo "Tidak ada data author atau category di database!\n";
            return;
        }

        // Insert 10 Buku
        $books = [];
        for ($i = 1; $i <= 20; $i++) {
            $books[] = [
                'id' => Str::uuid(),
                'title' => $faker->sentence(4), // Judul Random
                'cover_image' => $faker->imageUrl(200, 300, 'books'), // Gambar Random
                'description' => $faker->paragraph(), // Deskripsi Random
                'author_id' => $faker->randomElement($author_ids), // Ambil ID Penulis Acak
                'category_id' => $faker->randomElement($category_ids), // Ambil ID Kategori Acak
                'release_year' => $faker->year(), // Tahun Rilis Random
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('books')->insert($books);
        echo "10 Buku berhasil ditambahkan!\n";
    }
}
