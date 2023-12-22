<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoldingOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'settings_id',
        'value'
    ];

    public function setting(){
        return $this->belongsTo(Setting::class);
    }

    public function loaningPeriod(){
        return $this->hasOne(LoaningPeriod::class);
    }
}
