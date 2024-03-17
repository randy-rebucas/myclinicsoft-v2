<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalExamination extends Model
{
    use HasFactory;

    public $timestamps = FALSE;

    protected $fillable = [
        'vital_signs',
        'general_appearance',
        'systematic_findings',
        'notes',
        'encounter_id'
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
