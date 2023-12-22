<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VerificationCode;
use Carbon\Carbon;

class DeleteExpiredVerificationCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-expired-verification-codes';

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

        $fiveMinutesAgo = Carbon::now()->subMinutes(5);

        $expiredVerificationCodes = VerificationCode::whereDate('created_at', '<', $fiveMinutesAgo->toDateString())
            ->orWhere(function ($query) use ($fiveMinutesAgo) {
                $query->whereDate('created_at', $fiveMinutesAgo->toDateString())
                    ->whereTime('created_at', '<', $fiveMinutesAgo->toTimeString());
            })
            ->get()
            ->each(function ($verificationCode) {
                $verificationCode->delete();
            });
    }
}
