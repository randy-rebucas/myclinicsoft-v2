<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use App\Traits\RecordsActivity;

class Vital extends Model
{
    use RecordsActivity;
    protected $fillable = [
        'patient_id',
        'blood_pressure',
        'heart_rate',
        'temperature',
        'respiratory_rate',
        'oxygen_saturation',
        'blood_sugar',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'heart_rate' => 'integer',
            'temperature' => 'decimal:1',
            'respiratory_rate' => 'integer',
            'oxygen_saturation' => 'integer',
            'blood_sugar' => 'integer',
        ];
    }
    /**
     * Get the patient that owns these vitals.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

}
