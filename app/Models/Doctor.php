<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Doctor extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = FALSE;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'gender',
        'user_id',
        'meta'
    ];

    protected $appends = [
        'full_name'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function practice()
    {
        return $this->hasOne(Practice::class);
    }

    /**
     * Get all activities for the doctor
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function recordActivity($type, $description = null)
    {
        $this->activities()->create([
            'type' => $type,
            'description' => $description ?? "Doctor record was {$type}",
            'changes' => $this->getChanges(),
            'causer_id' => Auth::user()->id
        ]);
    }

    public function clinics()
    {
        return $this->belongsToMany(Clinic::class, 'clinic_doctors')
            ->withTimestamps()
            ->withPivot('is_primary');
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_doctors')
            ->withTimestamps()
            ->withPivot('is_active');
    }

    public function medRepresentatives()
    {
        return $this->belongsToMany(MedRepresentative::class, 'med_rep_doctors')
            ->withTimestamps()
            ->withPivot('is_active');
    }

    public function clinicDoctors()
    {
        return $this->hasMany(ClinicDoctor::class);
    }
}
