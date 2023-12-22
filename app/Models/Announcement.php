<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    protected $fillable = [
        'title',
        'content',
        'visibility',
        'librarian_id',
        'start_at',
        'end_at'
    ];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function librarian(){
        return $this->belongsTo(Patron::class);
    }
}
