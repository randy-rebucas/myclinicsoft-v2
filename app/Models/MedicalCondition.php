<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalCondition extends Model
{
    use HasFactory;

    public $timestamps = FALSE;
    
    protected $fillable = [
        'condition_name',
        'diagnosis_date',
        'status',
        'treatment_plan',
        'notes',
        'patient_id'
    ];

    protected $dates = [
        'diagnosis_date'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
