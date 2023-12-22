<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attendance';

    protected $fillable = [
        'patron_id',
        'librarian_id'
    ];

    public function librarian(){
        return $this->belongsTo(Patron::class, 'librarian_id');
    }

    public function patron(){
        return $this->belongsTo(Patron::class);
    }
}
