<?php

namespace App\Console\Commands;

use App\Mail\FixedDepositsMaturingMail;
use App\Models\Saving;
use App\SavingType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyMaturingFixedDeposits extends Command
{
    protected $signature = 'savings:notify-maturing-fds';

    protected $description = 'Email users about fixed deposits maturing in the current month';

    public function handle(): int
    {
        $monthStart = now()->startOfMonth();

        $savings = Saving::query()
            ->with('user')
            ->where('type', SavingType::FixedDeposit)
            ->whereYear('maturity_date', now()->year)
            ->whereMonth('maturity_date', now()->month)
            ->where(function ($query) use ($monthStart) {
                $query->whereNull('maturity_notified_at')
                    ->orWhere('maturity_notified_at', '<', $monthStart);
            })
            ->orderBy('maturity_date')
            ->get()
            ->filter(fn (Saving $saving) => $saving->user !== null);

        if ($savings->isEmpty()) {
            $this->info('No maturing FDs to notify.');

            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($savings->groupBy('user_id') as $userSavings) {
            $user = $userSavings->first()->user;

            Mail::to($user->email)->send(new FixedDepositsMaturingMail($user, $userSavings->values()));

            Saving::query()
                ->whereIn('id', $userSavings->pluck('id'))
                ->update(['maturity_notified_at' => now()]);

            $sent++;
            $this->line("Notified {$user->email} about {$userSavings->count()} FD(s).");
        }

        $this->info("Sent {$sent} notification email(s).");

        return self::SUCCESS;
    }
}
