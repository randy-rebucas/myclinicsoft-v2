<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Traits\RecordsActivity;
use App\Traits\Addressable;
use App\Traits\HasFullName;

class Doctor extends Model
{
    use HasFactory;
    use SoftDeletes;
    use RecordsActivity;
    use Addressable;
    use HasFullName;

    public $timestamps = FALSE;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'gender',
        'specialty',
        'license_number',
        'npi_number',
        'consultation_fee',
        'bio',
        'available_hours',
        'is_active',
        'user_id',
        'clinic_id',
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
            'consultation_fee' => 'decimal:2',
            'available_hours' => 'array',
            'meta' => 'array',
            'is_active' => 'boolean',
        ];
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
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

    public function clinicDoctors()
    {
        return $this->hasMany(ClinicDoctor::class);
    }

    public function encounters()
    {
        return $this->hasMany(Encounter::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

}
