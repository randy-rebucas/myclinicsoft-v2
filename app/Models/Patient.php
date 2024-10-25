<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $timestamps = FALSE;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'date_of_birth',
        'gender',
        'avatar',
        'user_id'
    ];

    protected $appends = [
        'full_name',
        'age'
    ];

    protected $dates = [
        'date_of_birth'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function getAgeAttribute()
    {
        return Carbon::parse($this->attributes['date_of_birth'])->age;
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
