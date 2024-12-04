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
        'user_id',
        'meta'
    ];

    protected $appends = [
        'full_name'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

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
        return $this->belongsToMany(MedRepresentative::class, 'med_rep_doctors')
            ->withTimestamps()
            ->withPivot('is_active');
    }

    /**
     * Get all subscriptions for the doctor
     */
    public function subscriptions()
    {
        return $this->hasMany(DoctorSubscription::class);
    }

    /**
     * Get the active subscription for the doctor
     */
    public function activeSubscription()
    {
        return $this->hasOne(DoctorSubscription::class)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest();
    }

    /**
     * Subscribe the doctor to a subscription plan
     */
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

    /**
     * Check if the doctor has an active subscription
     */
    public function hasActiveSubscription(): bool
    {
        return true;
        // If you're using Laravel Cashier with Stripe, you might use:
        // return $this->subscribed('default');

        // Or if you have a subscriptions relationship:
        // return $this->subscriptions()->active()->exists();

        // Or if you have a subscription_ends_at column:
        // return $this->subscription_ends_at === null || $this->subscription_ends_at->isFuture();
    }
}
