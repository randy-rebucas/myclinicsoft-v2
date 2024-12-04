<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedRepresentativeDoctor extends Model
{
    use HasFactory;

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'med_rep_doctors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'med_representative_id',
        'doctor_id',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the medRepresentative that owns the MedRepresentativeDoctor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function medRepresentative()
    {
        return $this->belongsTo(MedRepresentative::class);
    }

    /**
     * Get the doctor that owns the MedRepresentativeDoctor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
