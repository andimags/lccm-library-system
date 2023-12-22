<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
// use Spatie\Activitylog\Traits\LogsActivity;
// use Spatie\Activitylog\LogOptions;


class Patron extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, SoftCascadeTrait, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $primaryKey = 'id'; // specify the primary key field

    protected $fillable = [
        'id2',
        'librarian_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'temp_role',
        'email_verified_at',
        'registration_status',
        'display_mode'
    ];

    protected $softCascade = ['reservations'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'deleting' => \App\Events\PatronDeleted::class,
    ];

    // protected function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->logFillable('*')
    //         ->logOnlyDirty();
    // }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'borrower_id');
    }

    public function shelfItems()
    {
        return $this->hasMany(ShelfItem::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_patron');
    }

    public function tempCheckOutItems()
    {
        return $this->hasMany(TempCheckOutItem::class, 'librarian_id');
    }

    public function librarian()
    {
        return $this->belongsTo(Patron::class, 'librarian_id');
    }

    public function offSiteCirculations()
    {
        return $this->hasMany(OffSiteCirculation::class, 'borrower_id');
    }

    public function totalUnpaidFines()
    {
        return $this->offSiteCirculations()
            ->where('fines_status', 'unpaid')
            ->with('fines')
            ->get()
            ->sum(function ($circulation) {
                return $circulation->fines->sum('price');
            });
    }

    public function checkedOutCount()
    {
        return $this->offSiteCirculations()
            ->where('status', 'checked-out')
            ->count();
    }
}
