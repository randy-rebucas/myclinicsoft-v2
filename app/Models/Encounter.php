<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Traits\RecordsActivity;

class Encounter extends Model
{
    use HasFactory;
    use RecordsActivity;

    public $timestamps = FALSE;

    protected $fillable = [
        'chief_complaint',
        'encounter_date',
        'encounter_time',
        'appointment_type',
        'duration',
        'diagnosis',
        'treatment_plan',
        'follow_up_date',
        'status',
        'notes',
        'patient_id',
        'doctor_id',
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
            'encounter_time' => 'datetime:H:i',
            'follow_up_date' => 'date',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }


    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}
