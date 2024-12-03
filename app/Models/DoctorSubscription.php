<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSubscription extends Model
{
    protected $fillable = [
        'doctor_id',
        'subscription_plan_id',
        'starts_at',
        'ends_at',
        'status',
        'cancelled_at',
        'auto_renew'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'auto_renew' => 'boolean'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
