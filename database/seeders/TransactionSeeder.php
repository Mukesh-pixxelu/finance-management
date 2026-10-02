<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
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

        Transaction::factory()->for($user)->income()->create([
            'amount' => '2500.00',
            'description' => 'Salary',
            'occurred_on' => '2026-10-01',
        ]);

        Transaction::factory()->for($user)->expense()->create([
            'amount' => '180.50',
            'description' => 'Groceries',
            'occurred_on' => '2026-10-02',
        ]);
    }
}
