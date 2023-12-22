<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'report_type',
        'fields',
        'sort_by',
        'sort_order',
        'file_type',
        'librarian_id',
        'file_name'
    ];

    protected $dispatchesEvents = [
        'deleting' => \App\Events\ReportDeleted::class,
    ];

    public function librarian(){
        return $this->belongsTo(Patron::class, 'librarian_id');
    }
}
