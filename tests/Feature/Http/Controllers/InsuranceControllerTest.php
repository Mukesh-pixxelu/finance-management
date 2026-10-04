<?php

namespace Tests\Feature\Http\Controllers;

use App\InsuranceType;
use App\Models\Insurance;
use App\Models\User;
use App\PremiumFrequency;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class InsuranceControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_to_login_when_opening_insurances(): void
    {
        $this->get(route('insurances.index'))
            ->assertRedirectToRoute('login');
    }

    public function test_guest_is_redirected_to_login_when_adding_a_policy(): void
    {
        $this->post(route('insurances.store'), $this->payload())
            ->assertRedirectToRoute('login');

        $this->assertDatabaseCount('insurances', 0);
    }

    public function test_signed_in_user_records_their_own_policy(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user)
            ->post(route('insurances.store'), $this->payload([
                'user_id' => $other->id,
            ]))
            ->assertRedirectToRoute('insurances.index');

        $insurance = Insurance::query()->whereBelongsTo($user)->sole();

        $this->assertSame($user->id, $insurance->user_id);
        $this->assertSame(InsuranceType::Health, $insurance->type);
        $this->assertSame('Star Health', $insurance->provider);
        $this->assertSame('HDFC Bank', $insurance->bank_name);
        $this->assertSame('POL-12345678', $insurance->policy_number);
        $this->assertSame('Asha Sharma', $insurance->insured_person);
        $this->assertSame('500000.00', $insurance->sum_assured);
        $this->assertSame('12000.00', $insurance->premium_amount);
        $this->assertSame(PremiumFrequency::Yearly, $insurance->premium_frequency);
        $this->assertSame('2026-01-01', $insurance->start_date->toDateString());
        $this->assertSame('2027-01-01', $insurance->expiry_date->toDateString());
        $this->assertSame('12000.00', $insurance->yearlyPremium());
        $this->assertDatabaseMissing('insurances', [
            'user_id' => $other->id,
        ]);
    }

    public function test_insurances_page_lists_only_the_signed_in_users_policies(): void
    {
        $user = User::factory()->create();

        Insurance::factory()->for($user)->create([
            'policy_number' => 'POL-111',
            'provider' => 'LIC',
            'type' => InsuranceType::Life,
            'premium_amount' => '2000.00',
            'premium_frequency' => PremiumFrequency::Monthly,
            'expiry_date' => '2027-06-01',
        ]);
        Insurance::factory()->for($user)->create([
            'policy_number' => 'POL-222',
            'provider' => 'Star Health',
            'type' => InsuranceType::Health,
            'premium_amount' => '15000.00',
            'premium_frequency' => PremiumFrequency::Yearly,
            'expiry_date' => '2027-01-01',
        ]);
        Insurance::factory()->create([
            'policy_number' => 'POL-999',
        ]);

        $this->actingAs($user)
            ->get(route('insurances.index'))
            ->assertOk()
            ->assertSeeInOrder(['POL-222', 'POL-111'])
            ->assertDontSee('POL-999')
            ->assertSee('LIC')
            ->assertSee('Star Health')
            ->assertSee('Total cover:')
            ->assertSee('Yearly premium:')
            ->assertSee('24,000.00')
            ->assertSee('Edit');
    }

    public function test_owner_can_update_a_policy(): void
    {
        $user = User::factory()->create();
        $insurance = Insurance::factory()->for($user)->create([
            'policy_number' => 'POL-111',
            'provider' => 'LIC',
        ]);

        $this->actingAs($user)
            ->put(route('insurances.update', $insurance), $this->payload([
                'policy_number' => 'POL-111',
                'provider' => 'HDFC Life',
                'sum_assured' => '1000000.00',
                'notes' => 'Nominee: Rahul',
            ]))
            ->assertRedirectToRoute('insurances.index');

        $insurance->refresh();

        $this->assertSame('HDFC Life', $insurance->provider);
        $this->assertSame('1000000.00', $insurance->sum_assured);
        $this->assertSame('Nominee: Rahul', $insurance->notes);
    }

    public function test_another_user_cannot_edit_a_policy(): void
    {
        $insurance = Insurance::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('insurances.edit', $insurance))
            ->assertNotFound();
    }

    public function test_owner_can_delete_a_policy(): void
    {
        $user = User::factory()->create();
        $insurance = Insurance::factory()->for($user)->create();

        $this->actingAs($user)
            ->delete(route('insurances.destroy', $insurance))
            ->assertRedirectToRoute('insurances.index');

        $this->assertDatabaseMissing('insurances', [
            'id' => $insurance->id,
        ]);
    }

    public function test_policy_number_must_be_unique_per_user(): void
    {
        $user = User::factory()->create();

        Insurance::factory()->for($user)->create([
            'policy_number' => 'POL-111',
        ]);

        $this->actingAs($user)
            ->from(route('insurances.index'))
            ->post(route('insurances.store'), $this->payload([
                'policy_number' => 'POL-111',
            ]))
            ->assertRedirectToRoute('insurances.index')
            ->assertSessionHasErrors('policy_number');
    }

    public function test_signed_in_user_can_record_pmsby_accident_policy(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('insurances.store'), $this->payload([
                'type' => InsuranceType::Accident->value,
                'provider' => 'PMSBY (Pradhan Mantri Suraksha Bima Yojana)',
                'bank_name' => 'Punjab National Bank',
                'policy_number' => 'PMSBY-500087463379',
                'sum_assured' => '200000.00',
                'premium_amount' => '20.00',
                'premium_frequency' => PremiumFrequency::Yearly->value,
            ]))
            ->assertRedirectToRoute('insurances.index');

        $insurance = Insurance::query()->whereBelongsTo($user)->sole();

        $this->assertSame(InsuranceType::Accident, $insurance->type);
        $this->assertSame('PMSBY (Pradhan Mantri Suraksha Bima Yojana)', $insurance->provider);
        $this->assertSame('Punjab National Bank', $insurance->bank_name);

        $this->actingAs($user)
            ->get(route('insurances.index'))
            ->assertOk()
            ->assertSee('Accident')
            ->assertSee('PMSBY (Pradhan Mantri Suraksha Bima Yojana)')
            ->assertSee('Punjab National Bank');
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'type' => InsuranceType::Health->value,
            'provider' => 'Star Health',
            'bank_name' => 'HDFC Bank',
            'policy_number' => 'POL-12345678',
            'insured_person' => 'Asha Sharma',
            'sum_assured' => '500000.00',
            'premium_amount' => '12000.00',
            'premium_frequency' => PremiumFrequency::Yearly->value,
            'start_date' => '2026-01-01',
            'expiry_date' => '2027-01-01',
            'notes' => null,
            ...$overrides,
        ];
    }
}
