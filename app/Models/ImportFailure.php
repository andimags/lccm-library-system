<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportFailure extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'import_id',
        'values',
        'errors'
    ];

    protected $casts = [
        'values' => 'json',
        'errors' => 'json',
    ];

    public function import(){
        return $this->belongsTo(Import::class);
    }
}
