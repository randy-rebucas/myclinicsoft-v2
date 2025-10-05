<?php

namespace App\Traits;

use App\Models\Address;

trait Addressable
{
    /**
     * Get all addresses for the model.
     */
    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Get the default address for the model.
     */
    public function defaultAddress()
    {
        return $this->morphOne(Address::class, 'addressable')->where('default', true);
    }

    /**
     * Get the primary address for the model (default or first).
     */
    public function primaryAddress()
    {
        return $this->defaultAddress() ?? $this->addresses()->first();
    }
}
