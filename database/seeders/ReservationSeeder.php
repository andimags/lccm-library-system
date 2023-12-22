<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patronIds = \App\Models\Patron::pluck('id')->toArray();
        $copyId = \App\Models\Copy::where('availability', 'on loan')->pluck('id')->first(); // Use the first copy_id

        foreach (range(1, 1) as $index) {
            \App\Models\Reservation::create([
                'borrower_id' => $patronIds[array_rand($patronIds)],
                'copy_id' => $copyId, // Use the same $copyId for all reservations
                'status' => 'pending',
            ]);
        }
    }
}
