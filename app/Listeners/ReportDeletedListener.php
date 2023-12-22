<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;
use App\Events\ReportDeleted;

class ReportDeletedListener
{
    public function handle(ReportDeleted $event): void
    {
        if ($event->report->isForceDeleting()) {
            Storage::disk('public')->delete('reports/' . $event->report->file_name);
        }
    }
}
