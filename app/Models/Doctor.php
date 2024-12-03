<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Doctor extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = FALSE;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'gender',
        'user_id'
    ];

    protected $appends = [
        'full_name'
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function practice()
    {
        return $this->hasOne(Practice::class);
    }

    /**
     * Get all activities for the doctor
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function recordActivity($type, $description = null)
    {
        $this->activities()->create([
            'type' => $type,
            'description' => $description ?? "Doctor record was {$type}",
            'changes' => $this->getChanges(),
            'causer_id' => Auth::user()->id
        ]);
    }

    public function clinics()
    {
        return $this->belongsToMany(Clinic::class, 'clinic_doctors')
            ->withTimestamps()
            ->withPivot('is_primary');
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_doctors')
            ->withTimestamps()
            ->withPivot('is_active');
    }

    public function medRepresentatives()
    {
        return $this->belongsToMany(MedRepresentative::class, 'med_representative_doctors')
            ->withTimestamps()
            ->withPivot('is_active');
    }

    public function subscriptions()
    {
        return $this->hasMany(DoctorSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(DoctorSubscription::class)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest();
    }

    public function subscribe(SubscriptionPlan $plan)
    {
        $startDate = now();
        $endDate = $plan->billing_period === 'monthly'
            ? $startDate->addMonth()
            : $startDate->addYear();

        return $this->subscriptions()->create([
            'subscription_plan_id' => $plan->id,
            'starts_at' => $startDate,
            'ends_at' => $endDate,
            'status' => 'active',
            'auto_renew' => true
        ]);
    }
}
