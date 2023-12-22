<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShelfItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrower_id',
        'copy_id'
    ];

    public function borrower(){
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function copy(){
        return $this->belongsTo(Copy::class);
    }
}
