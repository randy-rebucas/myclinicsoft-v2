<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Encounter extends Model
{
    use HasFactory;

    public $timestamps = FALSE;

    protected $fillable = [
        'chief_complaint',
        'encounter_date',
        'notes',
        'patient_id'
    ];
    protected $dates = [
        'encounter_date'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
