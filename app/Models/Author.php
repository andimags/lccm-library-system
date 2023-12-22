<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = ['author'];

    public $timestamps = false;

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'author_collection');
    }
}
