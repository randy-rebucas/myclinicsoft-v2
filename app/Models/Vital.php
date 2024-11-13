<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vital extends Model
{
    protected $fillable = [
        'patient_id',
        'blood_pressure',
        'heart_rate',
        'temperature',
        'respiratory_rate',
        'oxygen_saturation',
        'blood_sugar',
    ];

    protected $casts = [
        'heart_rate' => 'integer',
        'temperature' => 'decimal:1',
        'respiratory_rate' => 'integer',
        'oxygen_saturation' => 'integer',
        'blood_sugar' => 'integer',
    ];

    /**
     * Get the patient that owns these vitals.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
