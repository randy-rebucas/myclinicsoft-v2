<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Department;

class Queue extends Model
{
    protected $fillable = [
        'patient_id',
        'department_id',
        'queue_number',
        'status',
        'priority',
        'called_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
