<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasStatuses;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;

class OffSiteCirculation extends Model
{
    use HasFactory, SoftDeletes, SoftCascadeTrait;

    // STATUSES
    // available, on loan, reserved

    protected $casts = [
        'due_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
    ];

    protected $fillable = [
        'reservation_id',
        'borrower_id',
        'librarian_id',
        'copy_id',
        'total_fines',
        'due_at',
        'grace_period_days',
        'checked_in_at',
        'checked_out_at',
        'fines_status',
        'status'
    ];

    protected $softCascade = ['renewals', 'fines'];

    public function fines()
    {
        return $this->hasMany(Fine::class);
    }

    public function borrower()
    {
        return $this->belongsTo(Patron::class, 'borrower_id');
    }

    public function librarian()
    {
        return $this->belongsTo(Patron::class, 'librarian_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function copy()
    {
        return $this->belongsTo(Copy::class);
    }

    public function renewals(){
        return $this->hasMany(Renewal::class);
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }
}
