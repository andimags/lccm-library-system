<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;

class Import extends Model
{
    use HasFactory, SoftDeletes, SoftCascadeTrait;

    protected $fillable = [
        'table',
        'success_count',
        'failed_count',
        'total_records',
        'librarian_id'
    ];

    protected $softCascade = ['importFailures'];

    public function librarian(){
        return $this->belongsTo(Patron::class, 'librarian_id');
    }

    public function importFailures(){
        return $this->hasMany(ImportFailure::class);
    }
}
