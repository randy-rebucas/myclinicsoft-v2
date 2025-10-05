<?php

namespace App\Traits;

trait HasFullName
{
    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get the full name as a method.
     */
    public function fullName()
    {
        return $this->getFullNameAttribute();
    }

    /**
     * Scope to search by full name.
     */
    public function scopeByFullName($query, $name)
    {
        return $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$name}%"]);
    }
}
