<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;

class VerificationCode extends Model
{
    use HasFactory, GeneratesUuid;

    protected $fillable = [
        'token',
        'code',
        'email',
        'type',
        'is_activated'
    ];

    public function uuidColumn(): string
    {
        return 'token';
    }
}
