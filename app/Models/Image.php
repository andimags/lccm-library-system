<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable =[
        'file_name',
        'imageable_id',
        'imageable_type',
    ];

    protected $dispatchesEvents = [
        'deleting' => \App\Events\ImageDeleted::class,
    ];

    public function imageable()
    {
        return $this->morphTo();
    }
}
