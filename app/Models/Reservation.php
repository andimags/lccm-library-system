<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Askedio\SoftCascade\Traits\SoftCascadeTrait;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'borrower_id',
        'copy_id',
        'check_out_before',
        'status'
    ];

    protected $casts = [
        'check_out_before' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'deleting' => \App\Events\ReservationDeleted::class,
    ];

    // protected $softCascade = ['reservationItems'];

    public function borrower(){
        return $this->belongsTo(Patron::class, 'borrower_id')->withTrashed();
    }

    public function copy(){
        return $this->belongsTo(Copy::class);
    }
}
