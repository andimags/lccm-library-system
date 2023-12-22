<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patron;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class RegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $roles = ['employee', 'faculty', 'student'];

        for ($i = 1; $i <= 20; $i++) {
            $patron = Patron::create([
                'id2' => $faker->unique()->randomNumber,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->email,
                'password' => $faker->password,
                'registration_status' => 'pending',
                'email_verified_at' => $faker->dateTimeBetween('-30 days', 'yesterday')
            ]);

            $patron->syncRoles($roles[rand(0, 2)]);
        }
    }
}
