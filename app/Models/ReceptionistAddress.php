<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceptionistAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'address_id',
        'receptionist_id'
    ];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function receptionist()
    {
        return $this->belongsTo(Receptionist::class);
    }
}
