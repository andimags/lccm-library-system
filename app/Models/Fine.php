<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;

class Fine extends Model
{
    use HasFactory, SoftDeletes, SoftCascadeTrait;

    protected $fillable = [
        'off_site_circulation_id',
        'reason',
        'note',
        'price',
        'librarian_id'
    ];

    protected $softCascade = [];

    public function offSiteCirculation(){
        return $this->belongsTo(OffSiteCirculation::class);
    }

    public function librarian()
    {
        return $this->belongsTo(Patron::class, 'librarian_id');
    }
}
