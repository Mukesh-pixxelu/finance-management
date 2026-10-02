<?php

namespace Database\Factories;

use App\Models\Saving;
use App\Models\User;
use App\SavingType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Saving>
 */
class SavingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => SavingType::FixedDeposit,
            'account_number' => fake()->unique()->numerify('############'),
            'interest_rate' => '7.10',
            'amount' => '100000.00',
            'start_date' => '2026-01-01',
            'maturity_date' => '2027-01-01',
            'interest_earned' => '7100.00',
        ];
    }

    public function savingsAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => SavingType::SavingsAccount,
        ]);
    }

    public function fixedDeposit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => SavingType::FixedDeposit,
        ]);
    }

    public function recurringDeposit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => SavingType::RecurringDeposit,
        ]);
    }
}
