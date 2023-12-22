<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(HoldingOptionSeeder::class);
        $this->call(CollectionSeeder::class);
        $this->call(PatronSeeder::class);
        $this->call(ReservationSeeder::class);
        $this->call(RegistrationSeeder::class);
    }
}
