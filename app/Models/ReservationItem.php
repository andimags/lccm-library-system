<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasStatuses;

class ReservationItem extends Model
{
    use HasFactory, SoftDeletes, HasStatuses;

    protected $fillable = [
        'reservation_id	',
        'collection_id',
        'copy'
    ];

    public function reservationItemable()
    {
        return $this->morphTo('reservation_itemable', 'reservation_itemable_type', 'reservation_itemable_id');
    }
    
    public function reservation()
    {
        return $this->belongsTo(RequestModel::class);
    }
}
