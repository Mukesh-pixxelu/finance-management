<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('account.edit'))
            ->assertRedirectToRoute('login');
    }

    public function test_user_can_update_name_and_email(): void
    {
        $user = User::factory()->create([
            'name' => 'Asha',
            'email' => 'asha@example.com',
        ]);
        $other = User::factory()->create([
            'email' => 'other@example.com',
        ]);

        $this->actingAs($user)
            ->put(route('account.update'), [
                'name' => 'Asha Rao',
                'email' => 'asha.rao@example.com',
                'id' => $other->id,
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'Profile saved.');

        $user->refresh();

        $this->assertSame('Asha Rao', $user->name);
        $this->assertSame('asha.rao@example.com', $user->email);
        $this->assertSame('other@example.com', $other->fresh()->email);
    }

    public function test_profile_rejects_an_email_that_belongs_to_someone_else(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create([
            'email' => 'taken@example.com',
        ]);

        $this->actingAs($user)
            ->from(route('account.edit'))
            ->put(route('account.update'), [
                'name' => $user->name,
                'email' => 'taken@example.com',
            ])
            ->assertRedirectToRoute('account.edit')
            ->assertInvalid([
                'email' => 'The email has already been taken.',
            ]);

        $this->assertSame($user->email, $user->fresh()->email);
        $this->assertSame('taken@example.com', $other->fresh()->email);
    }

    public function test_user_can_change_password_with_the_current_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('account.password'), [
                'current_password' => 'password',
                'password' => 'new-secret',
                'password_confirmation' => 'new-secret',
            ])
            ->assertRedirect()
            ->assertSessionHas('password_status', 'Password updated.');

        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('new-secret', $user->fresh()->password));
    }

    public function test_password_change_rejects_the_wrong_current_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('account.edit'))
            ->put(route('account.password'), [
                'current_password' => 'wrong-password',
                'password' => 'new-secret',
                'password_confirmation' => 'new-secret',
            ])
            ->assertRedirectToRoute('account.edit')
            ->assertInvalid([
                'current_password' => 'The password is incorrect.',
            ], 'password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
