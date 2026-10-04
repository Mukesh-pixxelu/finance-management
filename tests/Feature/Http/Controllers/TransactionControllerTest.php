<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\TransactionType;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_to_login_when_saving_a_transaction(): void
    {
        $this->post(route('transactions.store'), $this->payload())
            ->assertRedirectToRoute('login');

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_signed_in_user_records_their_own_transaction(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user)
            ->post(route('transactions.store'), $this->payload([
                'user_id' => $other->id,
            ]))
            ->assertRedirectToRoute('transactions.index');

        $transaction = Transaction::query()->whereBelongsTo($user)->sole();

        $this->assertSame($user->id, $transaction->user_id);
        $this->assertSame(TransactionType::Income, $transaction->type);
        $this->assertSame('1500.00', $transaction->amount);
        $this->assertSame('Salary', $transaction->description);
        $this->assertSame('2026-10-01', $transaction->occurred_on->toDateString());
        $this->assertDatabaseMissing('transactions', [
            'user_id' => $other->id,
        ]);
    }

    public function test_empty_transaction_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('transactions.index'))
            ->post(route('transactions.store'), [])
            ->assertInvalid([
                'type' => 'The type field is required.',
                'amount' => 'The amount field is required.',
                'description' => 'The description field is required.',
                'occurred_on' => 'The occurred on field is required.',
            ]);

        $this->assertDatabaseCount('transactions', 0);
    }

    #[DataProvider('invalidTransactions')]
    public function test_invalid_transaction_is_rejected(array $overrides, string $field, string $message): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('transactions.index'))
            ->post(route('transactions.store'), $this->payload($overrides))
            ->assertInvalid([
                $field => $message,
            ]);

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_guest_is_redirected_to_login_when_deleting_a_transaction(): void
    {
        $transaction = Transaction::factory()->create();

        $this->delete(route('transactions.destroy', $transaction))
            ->assertRedirectToRoute('login');

        $this->assertModelExists($transaction);
    }

    public function test_owner_deletes_a_transaction_and_returns_to_the_list(): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->for($user)->create();

        $this->actingAs($user)
            ->delete(route('transactions.destroy', $transaction))
            ->assertRedirectToRoute('transactions.index');

        $this->assertModelMissing($transaction);
    }

    public function test_another_user_cannot_delete_a_transaction(): void
    {
        $transaction = Transaction::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('transactions.destroy', $transaction))
            ->assertNotFound();

        $this->assertModelExists($transaction);
    }

    /**
     * @return array<string, array{0: array<string, mixed>, 1: string, 2: string}>
     */
    public static function invalidTransactions(): array
    {
        return [
            'type' => [['type' => 'transfer'], 'type', 'The selected type is invalid.'],
            'amount below minimum' => [['amount' => '0'], 'amount', 'The amount field must be at least 0.01.'],
            'too many decimal places' => [['amount' => '1.234'], 'amount', 'The amount field must have 0-2 decimal places.'],
            'description length' => [['description' => str_repeat('a', 256)], 'description', 'The description field must not be greater than 255 characters.'],
            'date' => [['occurred_on' => 'not-a-date'], 'occurred_on', 'The occurred on field must be a valid date.'],
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'type' => 'income',
            'amount' => '1500.00',
            'description' => 'Salary',
            'occurred_on' => '2026-10-01',
        ], $overrides);
    }
}
