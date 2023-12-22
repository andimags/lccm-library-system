<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'field',
        'value',
        // 'default_value'
    ];

    public function holdingOptions(){
        return $this->hasMany(HoldingOption::class);
    }
}
