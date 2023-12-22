<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InHouseCirculation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'librarian_id',
        'copy_id'
    ];

    public function librarian(){
        return $this->belongsTo(Patron::class, 'librarian_id');
    }

    public function copy(){
        return $this->belongsTo(Copy::class);
    }
}
