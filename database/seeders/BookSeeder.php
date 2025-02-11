<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author_id = DB::table('authors')->where('first_name', 'Jane')->value('id');
        $category_id = DB::table('categories')->where('name', 'action')->value('id');
        DB::table('books')->insert([
            [
                'id' => Str::uuid(),
                'title' => 'Perjalanan Hidup Mang Panca',
                'cover_image' => 'https://cdn.rri.co.id/berita/Samarinda/o/1724106965439-Buku_PNG_Transparent_With_Clear_Background_ID_100834___TopPNG/bgz2waop0l7mdt3.jpeg',
                'description' => 'lorem ipsum dolor sit amet',
                'author_id' => $author_id,
                'category_id' => $category_id,
                'release_year' => 2025,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Seeder Buku 2',
                'cover_image' => 'https://cdn.rri.co.id/berita/Samarinda/o/1724106965439-Buku_PNG_Transparent_With_Clear_Background_ID_100834___TopPNG/bgz2waop0l7mdt3.jpeg',
                'description' => 'lorem ipsum dolor sit amet',
                'author_id' => $author_id,
                'category_id' => $category_id,
                'release_year' => 2025,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
