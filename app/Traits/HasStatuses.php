<?php

namespace App\Traits;

use Spatie\ModelStatus\HasStatuses as OriginalHasStatuses;
use Spatie\ModelStatus\Exceptions\InvalidStatus;
use Spatie\ModelStatus\Events\StatusUpdated;

trait HasStatuses
{
    use OriginalHasStatuses;

    public function setStatus(string $name, ?int $librarianId = null, ?string $reason = null) : self
    {
        if (! $this->isValidStatus($name, $librarianId, $reason)) {
            throw InvalidStatus::create($name);
        }

        return $this->forceSetStatus($name, $librarianId, $reason);
    }

    public function forceSetStatus(string $name, ?int $librarianId = null, ?string $reason = null) : self
    {
        $oldStatus = $this->latestStatus();

        $newStatus = $this->statuses()->create([
            'name' => $name,
            'reason' => $reason,
            'librarian_id' => $librarianId
        ]);

        event(new StatusUpdated($oldStatus, $newStatus, $this));

        return $this;
    }
}





