<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Carbon\Carbon;

class CancelExpiredReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cancel-expired-reservations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reservations = Reservation::where('status', 'ready for check-out')
            ->where('check_out_before', '<=', Carbon::now()->toDateString())
            ->get();

        $reservations->each(function ($reservation) {
            $reservation->update([
                'status' => 'canceled',
                'check_out_before' => null
            ]);

            $newReservation = $reservation->copy->reservations()
                ->where('id', '<>', $reservation->id)
                ->where('status', 'pending')
                ->oldest()
                ->first();

            if (!$newReservation) {
                $reservation->copy()->update(['availability' => 'available']);
            } else {
                $newReservation->update([
                    'check_out_before' => Carbon::now()->addDays(4)->startOfDay(),
                    'status' => 'ready for check-out'
                ]);
            }
        });
    }
}
