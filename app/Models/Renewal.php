<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Renewal extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'old_due_at' => 'datetime',
        'new_due_at' => 'datetime',
        'old_grace_period_due_at' => 'datetime',
        'new_grace_period_due_at' => 'datetime'
    ];

    protected $fillable = [
        'old_due_at',
        'new_due_at',
        'off_site_circulation_id',
        'librarian_id'
    ];

    public function OffSiteCirculation()
    {
        return $this->belongsTo(OffSiteCirculation::class);
    }

    public function librarian()
    {
        return $this->belongsTo(Patron::class, 'librarian_id');
    }
}
