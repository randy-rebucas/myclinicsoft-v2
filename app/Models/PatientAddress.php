<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'address_id',
        'patient_id',
        'type',
        'default'
    ];

    protected $casts = [
        'default' => 'boolean',
    ];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
