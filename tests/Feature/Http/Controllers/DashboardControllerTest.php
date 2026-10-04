<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Saving;
use App\Models\User;
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
            ->assertSee('No bank assets yet');
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
}
