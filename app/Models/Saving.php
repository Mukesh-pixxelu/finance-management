<?php

namespace App\Models;

use App\SavingType;
use Carbon\CarbonInterface;
use Database\Factories\SavingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'type',
    'account_number',
    'bank_name',
    'interest_rate',
    'amount',
    'start_date',
    'maturity_date',
    'interest_earned',
])]
class Saving extends Model
{
    /** @use HasFactory<SavingFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => SavingType::class,
            'interest_rate' => 'decimal:2',
            'amount' => 'decimal:2',
            'start_date' => 'date',
            'maturity_date' => 'date',
            'interest_earned' => 'decimal:2',
            'maturity_notified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentValue(?CarbonInterface $on = null): string
    {
        if ($this->type === SavingType::RecurringDeposit) {
            return $this->depositedAmount($on);
        }

        return bcadd((string) ($this->amount ?? '0'), '0', 2);
    }

    public function depositedAmount(?CarbonInterface $on = null): string
    {
        return bcmul((string) ($this->amount ?? '0'), (string) $this->installmentsPaid($on), 2);
    }

    public function receivableAtMaturity(): string
    {
        $principal = $this->type === SavingType::RecurringDeposit
            ? bcmul((string) ($this->amount ?? '0'), (string) $this->installmentCount(), 2)
            : (string) ($this->amount ?? '0');

        return bcadd($principal, (string) ($this->interest_earned ?? '0'), 2);
    }

    public function installmentCount(): int
    {
        if ($this->start_date === null || $this->maturity_date === null) {
            return 0;
        }

        return $this->countInstallments($this->start_date, $this->maturity_date);
    }

    public function installmentsPaid(?CarbonInterface $on = null): int
    {
        if ($this->type !== SavingType::RecurringDeposit || $this->start_date === null) {
            return 0;
        }

        $on ??= now();

        if ($on->copy()->startOfDay()->lt($this->start_date->copy()->startOfDay())) {
            return 0;
        }

        $end = $this->maturity_date !== null && $on->copy()->startOfDay()->gt($this->maturity_date->copy()->startOfDay())
            ? $this->maturity_date
            : $on;

        return $this->countInstallments($this->start_date, $end);
    }

    private function countInstallments(CarbonInterface $start, CarbonInterface $end): int
    {
        $count = 0;
        $installmentOn = $start->copy()->startOfDay();
        $lastDay = $end->copy()->startOfDay();

        while ($installmentOn->lte($lastDay) && $count < 1200) {
            $count++;
            $installmentOn = $installmentOn->addMonth();
        }

        return $count;
    }
}
