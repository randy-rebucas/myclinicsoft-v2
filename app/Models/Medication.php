<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    public $timestamps = FALSE;

    protected $fillable = [
        'medication_name',
        'dosage',
        'frequency',
        'notes',
        'patient_id',
        'encounter_id'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
