<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'off_site_circulation_id',
        'borrower_id',
        'librarian_id',
        'status',
        'message',
        'remark'
    ];

    public function offSiteCirculation(){
        return $this->belongsTo(OffSiteCirculation::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function borrower(){
        return $this->belongsTo(Patron::class, 'borrower_id');
    }
}
