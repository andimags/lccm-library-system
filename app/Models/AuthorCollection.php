<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'collection_id'
    ];

    public $timestamps = false;
}
