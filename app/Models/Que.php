<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Que extends Model
{
    use HasFactory;

    protected $table = 'queing';

    protected $fillable = [
        'que_number',
        'metadata',
        'patient_id',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'collection',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
