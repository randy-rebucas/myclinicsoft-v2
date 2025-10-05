<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Traits\RecordsActivity;

class Allergy extends Model
{
    use HasFactory;
    use RecordsActivity;

    public $timestamps = FALSE;

    protected $fillable = [
        'allergen',
        'reaction',
        'severity',
        'notes',
        'patient_id'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

}
