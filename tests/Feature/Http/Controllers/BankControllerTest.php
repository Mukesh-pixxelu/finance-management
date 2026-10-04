<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Saving;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class BankControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('banks.show', 'hdfc-bank'))
            ->assertRedirectToRoute('login');
    }

    public function test_owner_can_open_bank_detail_page(): void
    {
        $user = User::factory()->create();

        Saving::factory()->for($user)->create([
            'bank_name' => 'HDFC Bank',
            'account_number' => '100200300',
            'amount' => '50000.00',
        ]);

        $this->actingAs($user)
            ->get(route('banks.show', 'hdfc-bank'))
            ->assertOk()
            ->assertSee('HDFC Bank')
            ->assertSee('100200300')
            ->assertSee('50,000.00');
    }

    public function test_another_users_bank_is_not_visible(): void
    {
        Saving::factory()->create([
            'bank_name' => 'HDFC Bank',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('banks.show', 'hdfc-bank'))
            ->assertNotFound();
    }
}
