<?php

namespace Database\Seeders;

use App\Models\Saving;
use App\Models\User;
use Illuminate\Database\Seeder;

class SavingSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
            ],
        );

        Saving::factory()->for($user)->fixedDeposit()->create([
            'account_number' => '100200300',
            'bank_name' => 'HDFC Bank',
            'interest_rate' => '7.10',
            'amount' => '100000.00',
            'start_date' => '2026-01-01',
            'maturity_date' => '2027-01-01',
            'interest_earned' => '7100.00',
        ]);

        Saving::factory()->for($user)->recurringDeposit()->create([
            'account_number' => '400500600',
            'bank_name' => 'ICICI Bank',
            'interest_rate' => '6.50',
            'amount' => '2000.00',
            'start_date' => '2026-02-01',
            'maturity_date' => '2027-02-01',
            'interest_earned' => '860.00',
        ]);
    }
}
