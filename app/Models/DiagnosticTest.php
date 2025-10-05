<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\RecordsActivity;

class DiagnosticTest extends Model
{
    use HasFactory, RecordsActivity;

    protected $fillable = [
        'test_name',
        'test_date',
        'results',
        'notes',
        'patient_id',
    ];

    protected $casts = [
        'test_date' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}