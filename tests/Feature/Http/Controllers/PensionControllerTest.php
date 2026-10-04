<?php

namespace Tests\Feature\Http\Controllers;

use App\ContributionFrequency;
use App\Models\Pension;
use App\Models\User;
use App\PensionScheme;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PensionControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_to_login_when_opening_pensions(): void
    {
        $this->get(route('pensions.index'))
            ->assertRedirectToRoute('login');
    }

    public function test_signed_in_user_records_atal_pension_yojana(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('pensions.store'), $this->payload())
            ->assertRedirectToRoute('pensions.index');

        $pension = Pension::query()->whereBelongsTo($user)->sole();

        $this->assertSame(PensionScheme::AtalPension, $pension->scheme);
        $this->assertSame('Punjab National Bank', $pension->bank_name);
        $this->assertSame('500087463379', $pension->pran);
        $this->assertSame('1000.00', $pension->contribution_amount);
        $this->assertSame(ContributionFrequency::Monthly, $pension->contribution_frequency);
        $this->assertSame('2026-01-01', $pension->start_date->toDateString());
        $this->assertSame('2046-01-01', $pension->pension_start_date->toDateString());
        $this->assertSame('5000.00', $pension->expected_pension);
        $this->assertSame('12000.00', $pension->yearlyContribution());
    }

    public function test_pensions_page_shows_dedicated_fields(): void
    {
        $user = User::factory()->create();

        Pension::factory()->for($user)->create([
            'pran' => '500087463379',
            'contribution_amount' => '1000.00',
            'expected_pension' => '5000.00',
            'expected_corpus' => '800000.00',
        ]);

        $this->actingAs($user)
            ->get(route('pensions.index'))
            ->assertOk()
            ->assertSee('Add a pension')
            ->assertSee('PRAN / Account number')
            ->assertSee('Contribution amount')
            ->assertSee('Expected pension')
            ->assertSee('Expected corpus')
            ->assertSee('Atal Pension Yojana')
            ->assertSee('500087463379')
            ->assertSee('Yearly contribution:');
    }

    public function test_owner_can_update_and_delete_a_pension(): void
    {
        $user = User::factory()->create();
        $pension = Pension::factory()->for($user)->create([
            'pran' => '500087463379',
        ]);

        $this->actingAs($user)
            ->put(route('pensions.update', $pension), $this->payload([
                'contribution_amount' => '1500.00',
                'expected_corpus' => '900000.00',
            ]))
            ->assertRedirectToRoute('pensions.index');

        $pension->refresh();
        $this->assertSame('1500.00', $pension->contribution_amount);
        $this->assertSame('900000.00', $pension->expected_corpus);

        $this->actingAs($user)
            ->delete(route('pensions.destroy', $pension))
            ->assertRedirectToRoute('pensions.index');

        $this->assertModelMissing($pension);
    }

    public function test_another_user_cannot_edit_a_pension(): void
    {
        $pension = Pension::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('pensions.edit', $pension))
            ->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'scheme' => PensionScheme::AtalPension->value,
            'bank_name' => 'Punjab National Bank',
            'pran' => '500087463379',
            'contribution_amount' => '1000.00',
            'contribution_frequency' => ContributionFrequency::Monthly->value,
            'start_date' => '2026-01-01',
            'pension_start_date' => '2046-01-01',
            'expected_pension' => '5000.00',
            'expected_corpus' => null,
            'notes' => null,
            ...$overrides,
        ];
    }
}
