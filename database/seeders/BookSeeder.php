<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Http;

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

        // Daftar query untuk buku terbaru 2024-2025
        $queries = [
            'new releases 2024',
            'bestseller 2024',
            'new fiction 2024',
            'new nonfiction 2024',
            'popular books 2025',
            'upcoming books 2024',
            'latest releases 2024'
        ];

        // Insert Buku
        $books = [];
        $count = 0;

        foreach ($queries as $query) {
            // Ambil data buku dari Google Books API
            $bookData = $this->getLatestBooks($query);

            foreach ($bookData as $book) {
                if ($count >= 20) break; // Batasi hingga 20 buku

                $books[] = [
                    'id' => Str::uuid(),
                    'title' => $book['title'],
                    'description' => $book['description'] ?? $faker->paragraph(),
                    'cover_image' => $book['cover_image'],
                    'author_id' => $faker->randomElement($author_ids),
                    'category_id' => $faker->randomElement($category_ids),
                    'release_year' => $book['release_year'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $count++;
            }

            if ($count >= 20) break;
        }

        // Jika data dari API kurang dari 20, tambahkan dengan data dummy
        while ($count < 20) {
            $books[] = [
                'id' => Str::uuid(),
                'title' => $faker->sentence(4),
                'description' => $faker->paragraph(),
                'cover_image' => 'https://via.placeholder.com/300x450?text=New+Book+2024',
                'author_id' => $faker->randomElement($author_ids),
                'category_id' => $faker->randomElement($category_ids),
                'release_year' => $faker->randomElement([2024, 2025]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $count++;
        }

        DB::table('books')->insert($books);
        echo "20 Buku terbaru 2024-2025 berhasil ditambahkan!\n";
    }

    /**
     * Mendapatkan data buku terbaru dari Google Books API
     * 
     * @param string $query
     * @return array
     */
    private function getLatestBooks(string $query): array
    {
        $books = [];

        try {
            // Menggunakan Google Books API untuk mencari buku terbaru
            $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
                'q' => $query,
                'maxResults' => 10,
                'orderBy' => 'newest'
            ]);

            $data = $response->json();

            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $volumeInfo = $item['volumeInfo'] ?? [];

                    // Ambil tahun publikasi
                    $publishedDate = $volumeInfo['publishedDate'] ?? '';
                    $year = substr($publishedDate, 0, 4);

                    // Hanya ambil buku 2024-2025
                    if ($year == '2024' || $year == '2025') {
                        $books[] = [
                            'title' => $volumeInfo['title'] ?? 'Buku Tanpa Judul',
                            'description' => $volumeInfo['description'] ?? null,
                            'cover_image' => $volumeInfo['imageLinks']['thumbnail'] ?? 'https://via.placeholder.com/300x450?text=No+Cover',
                            'release_year' => (int) $year
                        ];
                    }
                }
            }

            return $books;
        } catch (\Exception $e) {
            echo "Error saat mengambil data dari Google Books API: " . $e->getMessage() . "\n";
            return [];
        }
    }
}
