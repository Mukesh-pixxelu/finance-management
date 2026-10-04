<?php

namespace Database\Factories;

use App\Bank;
use App\InsuranceType;
use App\Insurer;
use App\Models\Insurance;
use App\Models\User;
use App\PremiumFrequency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Insurance>
 */
class InsuranceFactory extends Factory
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
            'type' => InsuranceType::Health,
            'provider' => fake()->randomElement(Insurer::options()),
            'bank_name' => fake()->randomElement(Bank::options()),
            'policy_number' => fake()->unique()->bothify('POL-########'),
            'insured_person' => fake()->name(),
            'sum_assured' => '500000.00',
            'premium_amount' => '12000.00',
            'premium_frequency' => PremiumFrequency::Yearly,
            'start_date' => '2026-01-01',
            'expiry_date' => '2027-01-01',
            'notes' => null,
        ];
    }
}
