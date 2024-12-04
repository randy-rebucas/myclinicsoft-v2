<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;

class Receptionist extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;

    public $timestamps = FALSE;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'gender',
        'user_id',
        'doctor_id'
    ];

    protected $appends = [
        'full_name'
    ];

     /**
     * Get the name of the index associated with the model.
     */
    public function searchableAs(): string
    {
        return 'receptionists_index';
    }

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get all activities for the receptionist
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function recordActivity($type, $description = null)
    {
        $this->activities()->create([
            'type' => $type,
            'description' => $description ?? "Receptionist record was {$type}",
            'changes' => $this->getChanges(),
            'causer_id' => Auth::user()->id
        ]);
    }
}
