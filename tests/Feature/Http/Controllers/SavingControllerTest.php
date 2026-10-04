<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Saving;
use App\Models\User;
use App\SavingType;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SavingControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_to_login_when_opening_savings(): void
    {
        $this->get(route('savings.index'))
            ->assertRedirectToRoute('login');
    }

    public function test_guest_is_redirected_to_login_when_adding_a_saving(): void
    {
        $this->post(route('savings.store'), $this->payload())
            ->assertRedirectToRoute('login');

        $this->assertDatabaseCount('savings', 0);
    }

    public function test_signed_in_user_records_their_own_fixed_deposit(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user)
            ->post(route('savings.store'), $this->payload([
                'user_id' => $other->id,
            ]))
            ->assertRedirectToRoute('savings.index');

        $saving = Saving::query()->whereBelongsTo($user)->sole();

        $this->assertSame($user->id, $saving->user_id);
        $this->assertSame(SavingType::FixedDeposit, $saving->type);
        $this->assertSame('100200300', $saving->account_number);
        $this->assertSame('HDFC Bank', $saving->bank_name);
        $this->assertSame('7.10', $saving->interest_rate);
        $this->assertSame('100000.00', $saving->amount);
        $this->assertSame('2026-01-01', $saving->start_date->toDateString());
        $this->assertSame('2027-01-01', $saving->maturity_date->toDateString());
        $this->assertSame('7100.00', $saving->interest_earned);
        $this->assertSame('107100.00', $saving->receivableAtMaturity());
        $this->assertDatabaseMissing('savings', [
            'user_id' => $other->id,
        ]);
    }

    public function test_savings_page_lists_only_the_signed_in_users_accounts(): void
    {
        $user = User::factory()->create();

        Saving::factory()->for($user)->fixedDeposit()->create([
            'account_number' => '100200300',
            'bank_name' => 'HDFC Bank',
            'maturity_date' => '2027-06-01',
            'interest_earned' => '7100.00',
        ]);
        Saving::factory()->for($user)->recurringDeposit()->create([
            'account_number' => '400500600',
            'bank_name' => 'ICICI Bank',
            'maturity_date' => '2027-01-01',
            'interest_earned' => '860.00',
        ]);
        Saving::factory()->create([
            'account_number' => '999888777',
        ]);

        $this->actingAs($user)
            ->get(route('savings.index'))
            ->assertSeeInOrder(['400500600', '100200300'])
            ->assertDontSee('999888777')
            ->assertSee('HDFC Bank')
            ->assertSee('ICICI Bank')
            ->assertSee('7,100.00')
            ->assertSee('1,07,100.00')
            ->assertSee('860.00')
            ->assertSee('Principal')
            ->assertSee('Monthly return')
            ->assertSee('Savings balance:')
            ->assertSee('Edit');
    }

    public function test_owner_can_update_a_saving(): void
    {
        $user = User::factory()->create();
        $saving = Saving::factory()->for($user)->create([
            'account_number' => '100200300',
            'bank_name' => 'Old Bank',
        ]);

        $this->actingAs($user)
            ->put(route('savings.update', $saving), $this->payload([
                'account_number' => '100200300',
                'bank_name' => 'India Post Payments Bank',
                'amount' => '125000.00',
                'maturity_date' => '2027-01-01',
                'interest_earned' => '7100.00',
            ]))
            ->assertRedirectToRoute('savings.index');

        $saving->refresh();

        $this->assertSame('India Post Payments Bank', $saving->bank_name);
        $this->assertSame('125000.00', $saving->amount);
    }

    public function test_another_user_cannot_edit_a_saving(): void
    {
        $saving = Saving::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('savings.edit', $saving))
            ->assertNotFound();
    }

    public function test_savings_page_escapes_the_account_number(): void
    {
        $user = User::factory()->create();
        $accountNumber = "<script>alert('xss')</script>";

        Saving::factory()->for($user)->create([
            'account_number' => $accountNumber,
        ]);

        $this->actingAs($user)
            ->get(route('savings.index'))
            ->assertSee($accountNumber)
            ->assertDontSee($accountNumber, false);
    }

    public function test_empty_saving_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('savings.index'))
            ->post(route('savings.store'), [])
            ->assertInvalid([
                'type' => 'The type field is required.',
                'account_number' => 'The account number field is required.',
                'bank_name' => 'The bank name field is required.',
                'interest_rate' => 'The interest rate field is required.',
                'amount' => 'The amount field is required.',
                'start_date' => 'The start date field is required.',
            ]);

        $this->assertDatabaseCount('savings', 0);
    }

    #[DataProvider('invalidSavings')]
    public function test_invalid_saving_is_rejected(array $overrides, string $field, string $message): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('savings.index'))
            ->post(route('savings.store'), $this->payload($overrides))
            ->assertInvalid([
                $field => $message,
            ]);

        $this->assertDatabaseCount('savings', 0);
    }

    public function test_duplicate_account_number_is_rejected_for_the_same_user(): void
    {
        $user = User::factory()->create();
        Saving::factory()->for($user)->create([
            'account_number' => '100200300',
        ]);

        $this->actingAs($user)
            ->from(route('savings.index'))
            ->post(route('savings.store'), $this->payload())
            ->assertInvalid([
                'account_number' => 'The account number has already been taken.',
            ]);

        $this->assertDatabaseCount('savings', 1);
    }

    public function test_another_user_may_use_the_same_account_number(): void
    {
        Saving::factory()->create([
            'account_number' => '100200300',
        ]);

        $this->actingAs(User::factory()->create())
            ->post(route('savings.store'), $this->payload())
            ->assertRedirectToRoute('savings.index');

        $this->assertDatabaseCount('savings', 2);
    }

    public function test_guest_is_redirected_to_login_when_deleting_a_saving(): void
    {
        $saving = Saving::factory()->create();

        $this->delete(route('savings.destroy', $saving))
            ->assertRedirectToRoute('login');

        $this->assertModelExists($saving);
    }

    public function test_owner_deletes_a_saving_and_returns_to_the_list(): void
    {
        $user = User::factory()->create();
        $saving = Saving::factory()->for($user)->create();

        $this->actingAs($user)
            ->delete(route('savings.destroy', $saving))
            ->assertRedirectToRoute('savings.index');

        $this->assertModelMissing($saving);
    }

    public function test_another_user_cannot_delete_a_saving(): void
    {
        $saving = Saving::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('savings.destroy', $saving))
            ->assertNotFound();

        $this->assertModelExists($saving);
    }

    /**
     * @return array<string, array{0: array<string, mixed>, 1: string, 2: string}>
     */
    public static function invalidSavings(): array
    {
        return [
            'type' => [['type' => 'loan'], 'type', 'The selected type is invalid.'],
            'interest rate below 1' => [
                ['interest_rate' => '0.99'],
                'interest_rate',
                'The interest rate field must be at least 1.',
            ],
            'interest rate above 20' => [
                ['interest_rate' => '20.01'],
                'interest_rate',
                'The interest rate field must not be greater than 20.',
            ],
            'non numeric account number' => [
                ['account_number' => 'FD100200300'],
                'account_number',
                'The account number field format is invalid.',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'type' => 'fd',
            'account_number' => '100200300',
            'bank_name' => 'HDFC Bank',
            'interest_rate' => '7.10',
            'amount' => '100000.00',
            'start_date' => '2026-01-01',
        ], $overrides);
    }
}
