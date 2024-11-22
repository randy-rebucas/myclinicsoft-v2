<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Encounter extends Model
{
    use HasFactory;

    public $timestamps = FALSE;

    protected $fillable = [
        'chief_complaint',
        'encounter_date',
        'notes',
        'patient_id'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'encounter_date' => 'date',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function vitals()
    {
        return $this->hasMany(Vital::class);
    }

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }
}
