<?php

namespace Tests\Feature\Policies;

use App\Models\Insurance;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class InsurancePolicyTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_owner_can_update_and_delete(): void
    {
        $user = User::factory()->create();
        $insurance = Insurance::factory()->for($user)->create();

        $this->assertTrue(Gate::forUser($user)->allows('update', $insurance));
        $this->assertTrue(Gate::forUser($user)->allows('delete', $insurance));
    }

    public function test_other_user_cannot_update_or_delete(): void
    {
        $insurance = Insurance::factory()->create();
        $other = User::factory()->create();

        $this->assertTrue(Gate::forUser($other)->denies('update', $insurance));
        $this->assertTrue(Gate::forUser($other)->denies('delete', $insurance));
    }
}
