<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoaningPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'holding_option_id',
        'role_id',
        'no_of_days',
        'grace_period_days'
    ];

    public function holdingOption(){
        return $this->belongsTo(HoldingOption::class);
    }
}
