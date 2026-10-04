<?php

namespace Database\Factories;

use App\Bank;
use App\ContributionFrequency;
use App\Models\Pension;
use App\Models\User;
use App\PensionScheme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pension>
 */
class PensionFactory extends Factory
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
            'scheme' => PensionScheme::AtalPension,
            'bank_name' => fake()->randomElement(Bank::options()),
            'pran' => fake()->unique()->numerify('############'),
            'contribution_amount' => '1000.00',
            'contribution_frequency' => ContributionFrequency::Monthly,
            'start_date' => '2026-01-01',
            'pension_start_date' => '2046-01-01',
            'expected_pension' => '5000.00',
            'expected_corpus' => null,
            'notes' => null,
        ];
    }
}
