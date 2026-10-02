<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_forgot_password_page_is_available_to_guests(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Forgot password')
            ->assertSee('Send reset link');
    }

    public function test_signed_in_user_is_sent_to_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('password.request'))
            ->assertRedirectToRoute('dashboard');
    }

    public function test_reset_link_is_sent_for_a_known_email(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('password.email'), [
            'email' => $user->email,
        ])->assertRedirect()
            ->assertSessionHas('status', 'If that account exists, a reset link is on its way.');

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user): bool {
            $url = $notification->toMail($user)->actionUrl;

            return str_contains($url, '/reset-password/')
                && str_contains($url, 'email='.urlencode($user->email));
        });
    }

    public function test_unknown_email_gets_the_same_message_and_no_notification(): void
    {
        Notification::fake();

        $this->post(route('password.email'), [
            'email' => 'missing@example.com',
        ])->assertRedirect()
            ->assertSessionHas('status', 'If that account exists, a reset link is on its way.');

        Notification::assertNothingSent();
    }

    public function test_user_can_choose_a_new_password_with_a_valid_token(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $token = Password::createToken($user);

        $this->get(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]))->assertOk()
            ->assertSee($user->email);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'brand-new-pass',
            'password_confirmation' => 'brand-new-pass',
        ])->assertRedirectToRoute('dashboard');

        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('brand-new-pass', $user->fresh()->password));
        $this->assertTrue(Hash::check('password', $other->fresh()->password));
    }

    public function test_invalid_token_does_not_change_the_password(): void
    {
        $user = User::factory()->create();

        $this->from(route('password.reset', ['token' => 'not-a-real-token']))
            ->post(route('password.update'), [
                'token' => 'not-a-real-token',
                'email' => $user->email,
                'password' => 'brand-new-pass',
                'password_confirmation' => 'brand-new-pass',
            ])
            ->assertInvalid([
                'email' => 'This password reset token is invalid.',
            ]);

        $this->assertGuest();
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
