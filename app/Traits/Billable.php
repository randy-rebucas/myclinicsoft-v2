<?php

namespace App\Traits;

use Carbon\Carbon;
use Exception;

trait Billable
{
    /**
     * The subscription plan amount.
     *
     * @var float
     */
    protected $planAmount = 0.0;

    /**
     * The billing cycle (monthly, yearly, etc.).
     *
     * @var string
     */
    protected $billingCycle = 'monthly';

    /**
     * Subscription start date.
     *
     * @var Carbon|null
     */
    protected $subscriptionStartDate = null;

    /**
     * Subscription end date.
     *
     * @var Carbon|null
     */
    protected $subscriptionEndDate = null;

    /**
     * Trial end date.
     *
     * @var Carbon|null
     */
    protected $trialEndsAt = null;

    /**
     * Set the subscription plan amount.
     *
     * @param float $amount
     * @return self
     */
    public function setPlanAmount(float $amount): self
    {
        $this->planAmount = $amount;
        return $this;
    }

    /**
     * Get the subscription plan amount.
     *
     * @return float
     */
    public function getPlanAmount(): float
    {
        return $this->planAmount;
    }

    /**
     * Set the billing cycle.
     *
     * @param string $cycle
     * @return self
     * @throws Exception
     */
    public function setBillingCycle(string $cycle): self
    {
        $allowedCycles = ['monthly', 'quarterly', 'biannual', 'yearly'];

        if (!in_array($cycle, $allowedCycles)) {
            throw new Exception("Invalid billing cycle. Allowed values: " . implode(', ', $allowedCycles));
        }

        $this->billingCycle = $cycle;
        return $this;
    }

    /**
     * Start a subscription.
     *
     * @param Carbon|null $startDate
     * @return self
     */
    public function startSubscription(?Carbon $startDate = null): self
    {
        $this->subscriptionStartDate = $startDate ?? now();
        $this->subscriptionEndDate = $this->calculateSubscriptionEndDate();
        return $this;
    }

    /**
     * Calculate subscription end date based on billing cycle.
     *
     * @return Carbon
     */
    protected function calculateSubscriptionEndDate(): Carbon
    {
        $startDate = $this->subscriptionStartDate ?? now();

        return match($this->billingCycle) {
            'monthly' => $startDate->copy()->addMonth(),
            'quarterly' => $startDate->copy()->addMonths(3),
            'biannual' => $startDate->copy()->addMonths(6),
            'yearly' => $startDate->copy()->addYear(),
            default => $startDate->copy()->addMonth(),
        };
    }

    /**
     * Start a trial period.
     *
     * @param int $days
     * @return self
     */
    public function startTrial(int $days): self
    {
        $this->trialEndsAt = now()->addDays($days);
        return $this;
    }

    /**
     * Check if the subscription is active.
     *
     * @return bool
     */
    public function hasActiveSubscription(): bool
    {
        if ($this->onTrial()) {
            return true;
        }

        return $this->subscriptionEndDate !== null
            && $this->subscriptionEndDate->isFuture();
    }

    /**
     * Check if the subscription is on trial.
     *
     * @return bool
     */
    public function onTrial(): bool
    {
        return $this->trialEndsAt !== null
            && $this->trialEndsAt->isFuture();
    }

    /**
     * Cancel the subscription.
     *
     * @param bool $immediately
     * @return self
     */
    public function cancelSubscription(bool $immediately = false): self
    {
        if ($immediately) {
            $this->subscriptionEndDate = now();
        }
        return $this;
    }

    /**
     * Renew the subscription.
     *
     * @return self
     */
    public function renewSubscription(): self
    {
        if ($this->hasActiveSubscription()) {
            $this->subscriptionEndDate = $this->calculateSubscriptionEndDate();
        } else {
            $this->startSubscription();
        }
        return $this;
    }

    /**
     * Get days until subscription ends.
     *
     * @return int|null
     */
    public function getDaysUntilExpiration(): ?int
    {
        if (!$this->subscriptionEndDate) {
            return null;
        }

        return now()->diffInDays($this->subscriptionEndDate);
    }
}
