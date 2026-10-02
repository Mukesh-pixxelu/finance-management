<?php

namespace Tests\Feature\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class TransactionPolicyTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_owner_may_delete_a_transaction(): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->for($user)->create();

        $response = Gate::forUser($user)->inspect('delete', $transaction);

        $this->assertTrue($response->allowed());
    }

    public function test_another_user_is_not_told_a_transaction_exists(): void
    {
        $transaction = Transaction::factory()->create();

        $response = Gate::forUser(User::factory()->create())->inspect('delete', $transaction);

        $this->assertTrue($response->denied());
        $this->assertSame(404, $response->status());
    }
}
