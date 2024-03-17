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

    protected $dates = [
        'date_administered'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
