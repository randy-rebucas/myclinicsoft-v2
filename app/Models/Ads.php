<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    // Specify the table name if it's different from the default 'ads'
    protected $table = 'ads';

    // Define which fields can be mass assigned
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'status',
        'start_date',
        'end_date',
        'url',
    ];

    // Define date fields to be automatically converted to Carbon instances
    protected $dates = [
        'start_date',
        'end_date'
    ];

    // Define attribute casting
    protected $casts = [
        'status' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    // Scope for active ads
    public function scopeActive($query)
    {
        return $query->where('status', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }
}
