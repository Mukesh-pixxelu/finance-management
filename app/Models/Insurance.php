<?php

namespace App\Models;

use App\InsuranceType;
use App\PremiumFrequency;
use Database\Factories\InsuranceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'type',
    'provider',
    'bank_name',
    'policy_number',
    'insured_person',
    'sum_assured',
    'premium_amount',
    'premium_frequency',
    'start_date',
    'expiry_date',
    'notes',
])]
class Insurance extends Model
{
    /** @use HasFactory<InsuranceFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => InsuranceType::class,
            'premium_frequency' => PremiumFrequency::class,
            'sum_assured' => 'decimal:2',
            'premium_amount' => 'decimal:2',
            'start_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function yearlyPremium(): string
    {
        $amount = (string) ($this->premium_amount ?? '0');
        $multiplier = (string) ($this->premium_frequency?->yearlyMultiplier() ?? 1);

        return bcmul($amount, $multiplier, 2);
    }
}
