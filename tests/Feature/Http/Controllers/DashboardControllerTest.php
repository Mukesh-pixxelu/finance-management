<?php

namespace Tests\Feature\Http\Controllers;

use App\ContributionFrequency;
use App\Models\Insurance;
use App\Models\Pension;
use App\Models\Saving;
use App\Models\User;
use App\PremiumFrequency;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirectToRoute('login');
    }

    public function test_dashboard_shows_empty_assets_state(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Total assets')
            ->assertSee('0.00')
            ->assertSee('No bank assets yet')
            ->assertSee('Insurance')
            ->assertSee('Pension')
            ->assertSee('No policies yet')
            ->assertSee('No pension accounts yet');
    }

    public function test_dashboard_shows_bank_cards_and_percentages(): void
    {
        $user = User::factory()->create();

        Saving::factory()->for($user)->create([
            'bank_name' => 'HDFC Bank',
            'amount' => '75000.00',
        ]);
        Saving::factory()->for($user)->create([
            'bank_name' => 'HDFC Bank',
            'amount' => '25000.00',
            'account_number' => '111222333',
        ]);
        Saving::factory()->for($user)->create([
            'bank_name' => 'ICICI Bank',
            'amount' => '100000.00',
            'account_number' => '444555666',
        ]);
        Saving::factory()->create([
            'bank_name' => 'Yes Bank',
            'amount' => '999999.00',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('2,00,000.00')
            ->assertSee('HDFC Bank')
            ->assertSee('ICICI Bank')
            ->assertSee('50.0%')
            ->assertSee('2 accounts')
            ->assertSee('1 account')
            ->assertDontSee('Yes Bank');
    }

    public function test_dashboard_shows_insurance_and_pension_summaries(): void
    {
        $user = User::factory()->create();

        Insurance::factory()->for($user)->create([
            'sum_assured' => '500000.00',
            'premium_amount' => '1000.00',
            'premium_frequency' => PremiumFrequency::Monthly,
        ]);
        Pension::factory()->for($user)->create([
            'contribution_amount' => '1000.00',
            'contribution_frequency' => ContributionFrequency::Monthly,
            'start_date' => now()->subMonths(5)->startOfMonth()->toDateString(),
            'pension_start_date' => now()->addYears(10)->toDateString(),
            'expected_pension' => '5000.00',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Cover & retirement')
            ->assertSee('5,00,000.00')
            ->assertSee('Total cover')
            ->assertSee('Contributed so far')
            ->assertSee('6,000.00')
            ->assertSee('6 contributions')
            ->assertSee('12,000.00')
            ->assertSee('5,000.00');
    }
}
