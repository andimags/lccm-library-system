<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;

class Copy extends Model
{
    use HasFactory, SoftDeletes, SoftCascadeTrait;

    protected $table = 'copies';

    protected $softCascade = [
        'tempCheckOutItems', 
        'offSiteCirculations', 
        'inHouseCirculations', 
        'reservations'
    ];

    protected $fillable = [
        'librarian_id',
        'collection_id',
        'barcode',
        'price',
        'fund',
        'vendor',
        'date_acquired',
        'availability',
        'call_prefix',
    ];

    public function collection(){
        return $this->belongsTo(Collection::class);
    }

    public function librarian(){
        return $this->belongsTo(Patron::class, 'librarian_id');
    }

    public function tempCheckOutItems(){
        return $this->hasMany(TempCheckOutItem::class);
    }

    public function offSiteCirculations(){
        return $this->hasMany(OffSiteCirculation::class);
    }

    public function inHouseCirculations(){
        return $this->hasMany(InHouseCirculation::class);
    }

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }
}
