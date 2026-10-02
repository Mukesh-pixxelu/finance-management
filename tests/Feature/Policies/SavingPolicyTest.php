<?php

namespace Tests\Feature\Policies;

use App\Models\Saving;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class SavingPolicyTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_owner_may_delete_a_saving(): void
    {
        $user = User::factory()->create();
        $saving = Saving::factory()->for($user)->create();

        $response = Gate::forUser($user)->inspect('delete', $saving);

        $this->assertTrue($response->allowed());
    }

    public function test_another_user_is_not_told_a_saving_exists(): void
    {
        $saving = Saving::factory()->create();

        $response = Gate::forUser(User::factory()->create())->inspect('delete', $saving);

        $this->assertTrue($response->denied());
        $this->assertSame(404, $response->status());
    }
}
