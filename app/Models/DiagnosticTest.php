<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosticTest extends Model
{
    use HasFactory;

    public $timestamps = FALSE;

    protected $fillable = [
        'test_name',
        'test_date',
        'results',
        'notes',
        'encounter_id'
    ];

    protected $dates = [
        'test_date'
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
