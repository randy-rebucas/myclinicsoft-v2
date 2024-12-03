<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'billing_period',
        'features'
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2'
    ];

    public function subscriptions()
    {
        return $this->hasMany(DoctorSubscription::class);
    }
}
