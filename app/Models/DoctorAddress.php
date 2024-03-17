<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'address_id',
        'doctor_id'
    ];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
