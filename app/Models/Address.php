<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'line_1',
        'line_2',
        'district',
        'city_id',
        'postal_code'
    ];

    protected $appends = [
        'complete_address'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function patient_address()
    {
        return $this->belongsTo(PatientAddress::class);
    }

    public function getCompleteAddressAttribute()
    {
        $address = $this->line_1 . ', ' . $this->line_2;
        $address .= $this->district . ', ' . $this->city->name;
        $address .= $this->city->country->name . ', ' . $this->postal_code;
        return $address;
    }
}
