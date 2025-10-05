<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\RecordsActivity;

class Immunization extends Model
{
    use HasFactory, RecordsActivity;

    protected $fillable = [
        'vaccine_name',
        'date_administered',
        'lot_number',
        'manufacturer',
        'notes',
        'patient_id',
    ];

    protected $casts = [
        'date_administered' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}