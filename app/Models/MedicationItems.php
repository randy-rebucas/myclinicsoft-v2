<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicationItems extends Model
{
    protected $fillable = [
        'medication_name',
        'dosage',
        'frequency',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }
}
