<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use App\Traits\RecordsActivity;
use App\Traits\Addressable;

class Clinic extends Model
{
    use HasFactory;
    use RecordsActivity;
    use Addressable;

    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'zip',
        'phone',
        'email',
        'website',
        'license_number',
        'tax_id',
        'logo',
        'operating_hours',
        'emergency_contact',
        'description',
        'is_active'
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'is_active' => 'boolean'
    ];

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'clinic_doctors')
            ->withTimestamps()
            ->withPivot('is_primary');
    }

    /**
     * Get the doctors that belong to the clinic.
     */
    public function clinicDoctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class, 'clinic_doctors')
            ->withPivot('is_primary')
            ->withTimestamps();
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
