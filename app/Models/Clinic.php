<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'zip',
        'phone',
        'email',
        'description',
        'is_active'
    ];

    protected $casts = [
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
}
