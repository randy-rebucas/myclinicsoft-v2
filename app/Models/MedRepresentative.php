<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class MedRepresentative extends Model
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

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    /**
     * Get all activities for the medRepresentative
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    protected static function booted()
    {
        static::created(function ($medRepresentative) {
            $medRepresentative->recordActivity('created');
        });

        static::updated(function ($medRepresentative) {
            $medRepresentative->recordActivity('updated');
        });

        static::deleted(function ($medRepresentative) {
            $medRepresentative->recordActivity('deleted');
        });
    }

    public function recordActivity($type, $description = null)
    {
        $this->activities()->create([
            'type' => $type,
            'description' => $description ?? "MedRepresentative record was {$type}",
            'changes' => $this->getChanges(),
            'causer_id' => Auth::user()->id
        ]);
    }
}
