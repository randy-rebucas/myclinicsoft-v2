<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['created', 'updated', 'deleted']),
            'description' => $this->faker->sentence(),
            'changes' => ['field' => $this->faker->word()],
            'causer_id' => User::factory()
        ];
    }
}
