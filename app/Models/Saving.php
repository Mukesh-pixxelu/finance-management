<?php

namespace App\Models;

use App\SavingType;
use Database\Factories\SavingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'type',
    'account_number',
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
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function receivableAtMaturity(): ?string
    {
        if ($this->type === SavingType::RecurringDeposit) {
            return null;
        }

        return bcadd($this->amount, $this->interest_earned, 2);
    }
}
