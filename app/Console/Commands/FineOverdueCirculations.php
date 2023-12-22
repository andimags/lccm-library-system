<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class FineOverdueCirculations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fine-overdue-circulations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // CHECK IF ENABLE AUTOMATIC FINES IS SET TO 'YES'
        $enableAutomaticFines = \App\Models\Setting::where('field', 'enable_automatic_fines')->first();

        if ($enableAutomaticFines->value == 'no') {
            return;
        }

        $overdueCirculations = \App\Models\OffSiteCirculation::where('status', 'checked-out')
            ->where('due_at', '<=', Carbon::today()->setTime(8, 0, 0));

        $currentTime = Carbon::now()->format('H');
        $targetTime = Carbon::now()->setTime(8, 0)->format('H');

        if ($currentTime === $targetTime) {
            // Current time is 8:00 AM (Fine both important & unimportant copies)
            $overdueCirculations = $overdueCirculations->get();

            foreach ($overdueCirculations as $circulation) {
                // Calculate due date with grace period
                $dueDateWithGrace = Carbon::parse($circulation->due_at)
                    ->addDays($circulation->grace_period_days);

                if ($dueDateWithGrace->lt(Carbon::now())) {
                    $circulation->fines()->create([
                        'reason' => 'Overdue Penalty',
                        'price' => 5.00
                    ]);

                    $circulation->total_fines = $circulation->fines()->sum('price');
                    $circulation->save();
                }
            }
        } else {
            // Current time is not 8:00 AM (Fine important copies only)
            $importantCirculations = $overdueCirculations
                ->whereHas('copy', function ($query) {
                    $query->where('call_prefix', 'important');
                })
                ->get();


            foreach ($importantCirculations as $circulation) { {
                    // Calculate due date with grace period
                    $dueDateWithGrace = Carbon::parse($circulation->due_at)
                        ->addDays($circulation->grace_period_days);

                    // Check if the due date (plus grace period) is after today
                    if ($dueDateWithGrace->lt(Carbon::now())) {
                        $circulation->fines()->create([
                            'reason' => 'Overdue Penalty',
                            'price' => 5.00
                        ]);

                        $circulation->total_fines = $circulation->fines()->sum('price');
                        $circulation->save();
                    }
                }
            }
        }
    }
}
