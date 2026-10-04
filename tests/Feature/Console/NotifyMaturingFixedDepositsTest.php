<?php

namespace Tests\Feature\Console;

use App\Mail\FixedDepositsMaturingMail;
use App\Models\Saving;
use App\Models\User;
use App\SavingType;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NotifyMaturingFixedDepositsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_emails_users_about_fds_maturing_this_month_once(): void
    {
        Mail::fake();
        Carbon::setTestNow('2026-10-15 10:00:00');

        $user = User::factory()->create([
            'email' => 'owner@example.com',
        ]);

        $maturing = Saving::factory()->for($user)->fixedDeposit()->create([
            'account_number' => '111222333',
            'bank_name' => 'HDFC Bank',
            'maturity_date' => '2026-10-28',
        ]);

        Saving::factory()->for($user)->fixedDeposit()->create([
            'account_number' => '444555666',
            'maturity_date' => '2026-11-05',
        ]);

        Saving::factory()->for($user)->recurringDeposit()->create([
            'account_number' => '777888999',
            'maturity_date' => '2026-10-20',
        ]);

        $this->artisan('savings:notify-maturing-fds')
            ->assertSuccessful();

        Mail::assertSent(FixedDepositsMaturingMail::class, function (FixedDepositsMaturingMail $mail) use ($user) {
            return $mail->hasTo($user->email)
                && $mail->savings->count() === 1
                && $mail->savings->first()->account_number === '111222333';
        });

        $this->assertNotNull($maturing->fresh()->maturity_notified_at);

        Mail::fake();

        $this->artisan('savings:notify-maturing-fds')
            ->assertSuccessful();

        Mail::assertNothingSent();

        Carbon::setTestNow();
    }
}
