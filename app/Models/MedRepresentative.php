<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;

class MedRepresentative extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = FALSE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'gender',
        'user_id'
    ];

    /**
     * The attributes that should be appended.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'full_name'
    ];

    /**
     * Get the name of the index associated with the model.
     */
    public function searchableAs(): string
    {
        return 'med_representatives_index';
    }

    /**
     * Get the address associated with the medRepresentative
     */
    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /**
     * Get the full name attribute
     */
    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * Get the user associated with the medRepresentative
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all activities for the medRepresentative
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * Record an activity for the medRepresentative
     */
    public function recordActivity($type, $description = null)
    {
        $this->activities()->create([
            'type' => $type,
            'description' => $description ?? "MedRepresentative record was {$type}",
            'changes' => $this->getChanges(),
            'causer_id' => Auth::user()->id
        ]);
    }

    /**
     * Get the doctors associated with the medRepresentative
     */
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'med_rep_doctors')
            ->withTimestamps()
            ->withPivot('is_active');
    }
}
