<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedRepresentativeDoctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'med_representative_id',
        'doctor_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function medRepresentative()
    {
        return $this->belongsTo(MedRepresentative::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
