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
        'label'
    ];

    public function addressable()
    {
        return $this->morphTo();
    }

    public function getCompleteAddressAttribute()
    {
        $address = $this->address_line_1 . ', ' . $this->address_line_2;
        $address .= $this->city . ', ' . $this->state;
        $address .= $this->country . ', ' . $this->postal_code;
        return $address;
    }
}
