<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Events\ImageDeleted;

class ImageDeletedListener
{
    public function handle(ImageDeleted $event)
    {
        $folderNames = [
            'App\Models\Patron' => 'patrons',
            'App\Models\Collection' => 'collections',
            'App\Models\Announcement' => 'announcements'
        ];

        Storage::disk('public')->delete('images/' . $folderNames[$event->image->imageable_type] . '/' . $event->image->file_name);
        // Log::info('ImageDeleted event was fired and handled.');
    }
}
