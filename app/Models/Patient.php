<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $timestamps = FALSE;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'date_of_birth',
        'height',
        'weight',
        'gender',
        'avatar',
        'user_id'
    ];

    protected $appends = [
        'full_name',
        'age'
    ];

    protected $dates = [
        'date_of_birth'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function encounters()
    {
        return $this->hasMany(Encounter::class);
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? Carbon::parse($this->attributes['date_of_birth'])->age : null;
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function full_name()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function vitals()
    {
        return $this->hasMany(Vital::class);
    }
    /**
     * Get all activities for the patient
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    protected static function booted()
    {
        static::created(function ($patient) {
            $patient->recordActivity('created');
        });

        static::updated(function ($patient) {
            $patient->recordActivity('updated');
        });

        static::deleted(function ($patient) {
            $patient->recordActivity('deleted');
        });
    }

    public function recordActivity($type, $description = null)
    {
        $this->activities()->create([
            'type' => $type,
            'description' => $description ?? "Patient record was {$type}",
            'changes' => $this->getChanges(),
            'causer_id' => Auth::user()->id
        ]);
    }
}
