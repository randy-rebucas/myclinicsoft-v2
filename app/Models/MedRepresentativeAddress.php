<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedRepresentativeAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'address_id',
        'med_representative_id'
    ];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function med_representative()
    {
        return $this->belongsTo(MedRepresentative::class);
    }
}
