<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Receptionist extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $timestamps = FALSE;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'gender',
        'user_id'
    ];

    protected $appends = [
        'full_name'
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all activities for the receptionist
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    protected static function booted()
    {
        static::created(function ($receptionist) {
            $receptionist->recordActivity('created');
        });

        static::updated(function ($receptionist) {
            $receptionist->recordActivity('updated');
        });

        static::deleted(function ($receptionist) {
            $receptionist->recordActivity('deleted');
        });
    }

    public function recordActivity($type, $description = null)
    {
        $this->activities()->create([
            'type' => $type,
            'description' => $description ?? "Receptionist record was {$type}",
            'changes' => $this->getChanges(),
            'causer_id' => Auth::user()->id
        ]);
    }
}
