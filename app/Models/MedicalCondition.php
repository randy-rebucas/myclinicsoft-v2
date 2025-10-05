<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\RecordsActivity;

class MedicalCondition extends Model
{
    use HasFactory, RecordsActivity;

    protected $fillable = [
        'condition_name',
        'diagnosis_date',
        'status',
        'treatment_plan',
        'notes',
        'patient_id',
    ];

    protected $casts = [
        'diagnosis_date' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}