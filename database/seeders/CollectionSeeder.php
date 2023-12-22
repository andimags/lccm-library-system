<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for($y = 1; $y <= 10; $y++) {
            $collection = \App\Models\Collection::create([
                'title' => $faker->sentence(5), // Generate a book title
                'format' => 'Book'
            ]);

            $randomNumber = rand(1, 10);
            
            for($x = 1; $x <= $randomNumber; $x++){
                $collection->copies()->create([
                    'barcode' => rand(100000, 999999)
                ]);
            }
        }
    }
}
