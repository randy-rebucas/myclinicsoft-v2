<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function getCompleteAddressAttribute()
    {
        $address = $this->line_1 . ', ' . $this->line_2;
        $address .= $this->district . ', ' . $this->city->name;
        $address .= $this->city->country->name . ', ' . $this->postal_code;
        return $address;
    }
}
