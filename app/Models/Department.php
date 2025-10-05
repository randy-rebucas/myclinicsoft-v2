<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only active departments
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the queues for this department
     */
    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    /**
     * Get the doctors for this department
     */
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
}
