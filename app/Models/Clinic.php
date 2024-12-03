<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'description'
    ];

    public function clinicDoctors()
    {
        return $this->belongsToMany(Doctor::class, 'clinic_doctors')
            ->withTimestamps()
            ->withPivot('is_primary');
    }
}
