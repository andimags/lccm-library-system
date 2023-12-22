<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patron;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class PatronSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $roles = [['librarian', 'employee'], 'employee', 'faculty', 'student'];

        $patron = Patron::create([
            'id2' => '20200125264',
            'first_name' => 'Andrea',
            'last_name' => 'Magsumbol',
            'email' => 'andimagsumbol@gmail.com',
            'display_mode' => 'night'
        ]);

        $patron->assignRole(['librarian', 'employee']);

        for ($i = 1; $i <= 10; $i++) {
            $patron = Patron::create([
                'id2' => $faker->unique()->randomNumber,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->email,
            ]);

            $patron->syncRoles($roles[rand(0, 3)]);
        }
    }
}
