<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jackiedo\Cart\Traits\CanUseCart;     // Trait
use Jackiedo\Cart\Contracts\UseCartable; // Interface
use Askedio\SoftCascade\Traits\SoftCascadeTrait;

class Collection extends Model implements UseCartable
{
    use HasFactory, SoftDeletes, CanUseCart, SoftCascadeTrait;

    protected $softCascade = ['copies'];

    protected $fillable = [
        'librarian_id',
        'format',
        'title',
        'edition',
        'series_title',
        'isbn',
        'publication_place',
        'publisher',
        'copyright_year',
        'physical_description',
        'call_main',
        'call_cutter',
        'call_suffix'
    ];

    public function copies()
    {
        return $this->hasMany(Copy::class);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'author_collection');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'collection_subject');
    }

    public function subtitles()
    {
        return $this->hasMany(Subtitle::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function librarian()
    {
        return $this->belongsTo(Patron::class, 'librarian_id');
    }
}
