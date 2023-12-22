<?php

namespace App\Listeners;

use App\Events\PatronDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;

class PatronDeletedListener
{
    /**
     * Handle the event.
     */
    public function handle(PatronDeleted $event): void
    {
        $reservations = $event->patron->reservations()->get();

        foreach($reservations as $reservation){
            if ($reservation->status == 'ready for check-out') {
                $reservation->update([
                    'status' => 'canceled',
                    'expired_at' => null
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
                        'expired_at' => Carbon::now()->addDays(3)->startOfDay(),
                        'status' => 'ready for check-out'
                    ]);
                }
            } else if ($reservation->status == 'pending'){
                $reservation->update([
                    'status' => 'canceled',
                ]);
            }
        }
    }
}
