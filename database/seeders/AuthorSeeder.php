<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i = 0; $i < 20; $i++) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $email = strtolower($firstName . '.' . $lastName . '@example.com'); // Email berbasis nama

            DB::table('authors')->insert([
                'id' => Str::uuid(),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'no_telp' => '62' . $faker->numerify('8##########'), // Format nomor HP Indonesia
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
