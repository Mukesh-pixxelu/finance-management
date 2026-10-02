<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Transaction;
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

    public function test_dashboard_shows_zero_balance_when_there_are_no_transactions(): void
    {
        $this->travelTo('2026-10-02');

        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertSee('0.00')
            ->assertSee('No transactions yet.')
            ->assertSee('Add a transaction to see this chart.')
            ->assertSee('2026-10-02');
    }

    public function test_dashboard_shows_balance_and_hides_another_users_transactions(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->income()->create([
            'amount' => '100.50',
            'description' => 'Salary',
            'occurred_on' => '2026-10-01',
        ]);
        Transaction::factory()->for($user)->expense()->create([
            'amount' => '40.25',
            'description' => 'Lunch',
            'occurred_on' => '2026-10-02',
        ]);
        Transaction::factory()->income()->create([
            'description' => 'Secret bonus',
            'amount' => '999.00',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertSee('60.25')
            ->assertSee('100.50')
            ->assertSee('40.25')
            ->assertSeeInOrder(['71.4%', '28.6%'])
            ->assertSeeInOrder(['Lunch', 'Salary'])
            ->assertDontSee('Secret bonus');
    }

    public function test_dashboard_shows_a_negative_balance(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->income()->create([
            'amount' => '10.00',
            'description' => 'Gift',
            'occurred_on' => '2026-10-01',
        ]);
        Transaction::factory()->for($user)->expense()->create([
            'amount' => '25.00',
            'description' => 'Rent',
            'occurred_on' => '2026-10-02',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertSee('-15.00')
            ->assertSeeInOrder(['28.6%', '71.4%']);
    }

    public function test_dashboard_escapes_the_transaction_description(): void
    {
        $user = User::factory()->create();
        $description = "<script>alert('xss')</script>";

        Transaction::factory()->for($user)->create([
            'description' => $description,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertSee($description)
            ->assertDontSee($description, false);
    }
}
