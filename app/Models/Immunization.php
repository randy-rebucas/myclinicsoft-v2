<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Immunization extends Model
{
    use HasFactory;
    public $timestamps = FALSE;

    protected $fillable = [
        'vaccine_name',
        'date_administered',
        'administrator',
        'notes',
        'patient_id'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_administered' => 'date',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
