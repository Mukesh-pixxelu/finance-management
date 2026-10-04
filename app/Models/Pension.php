<?php

namespace App\Models;

use App\ContributionFrequency;
use App\PensionScheme;
use Database\Factories\PensionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable([
    'scheme',
    'bank_name',
    'pran',
    'contribution_amount',
    'contribution_frequency',
    'start_date',
    'pension_start_date',
    'expected_pension',
    'expected_corpus',
    'notes',
])]
class Pension extends Model
{
    /** @use HasFactory<PensionFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheme' => PensionScheme::class,
            'contribution_frequency' => ContributionFrequency::class,
            'contribution_amount' => 'decimal:2',
            'expected_pension' => 'decimal:2',
            'expected_corpus' => 'decimal:2',
            'start_date' => 'date',
            'pension_start_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function yearlyContribution(): string
    {
        $amount = (string) ($this->contribution_amount ?? '0');
        $multiplier = (string) ($this->contribution_frequency?->yearlyMultiplier() ?? 1);

        return bcmul($amount, $multiplier, 2);
    }

    public function contributionsSoFar(?Carbon $asOf = null): int
    {
        if ($this->start_date === null) {
            return 0;
        }

        $start = $this->start_date->copy()->startOfDay();
        $end = ($asOf ?? now())->copy()->startOfDay();

        if ($this->pension_start_date !== null && $this->pension_start_date->lte($end)) {
            $end = $this->pension_start_date->copy()->startOfDay();
        }

        if ($end->lt($start)) {
            return 0;
        }

        $paid = $this->inclusivePeriods($start, $end);
        $planned = $this->totalContributions();

        return $planned > 0 ? min($paid, $planned) : $paid;
    }

    public function totalContributions(): int
    {
        if ($this->start_date === null || $this->pension_start_date === null) {
            return 0;
        }

        $start = $this->start_date->copy()->startOfDay();
        $end = $this->pension_start_date->copy()->startOfDay();

        if ($end->lte($start)) {
            return 0;
        }

        return $this->exclusivePeriods($start, $end);
    }

    public function contributionsRemaining(?Carbon $asOf = null): int
    {
        return max(0, $this->totalContributions() - $this->contributionsSoFar($asOf));
    }

    public function contributionProgressPercent(?Carbon $asOf = null): float
    {
        $total = $this->totalContributions();

        if ($total === 0) {
            return 0.0;
        }

        return min(100, round(($this->contributionsSoFar($asOf) / $total) * 100, 1));
    }

    public function totalContributedSoFar(?Carbon $asOf = null): string
    {
        return bcmul(
            (string) ($this->contribution_amount ?? '0'),
            (string) $this->contributionsSoFar($asOf),
            2,
        );
    }

    private function inclusivePeriods(Carbon $start, Carbon $end): int
    {
        return match ($this->contribution_frequency) {
            ContributionFrequency::Monthly => (int) $start->diffInMonths($end) + 1,
            ContributionFrequency::Quarterly => intdiv((int) $start->diffInMonths($end), 3) + 1,
            ContributionFrequency::Yearly => (int) $start->diffInYears($end) + 1,
            default => 0,
        };
    }

    private function exclusivePeriods(Carbon $start, Carbon $end): int
    {
        return match ($this->contribution_frequency) {
            ContributionFrequency::Monthly => max(0, (int) $start->diffInMonths($end)),
            ContributionFrequency::Quarterly => max(0, intdiv((int) $start->diffInMonths($end), 3)),
            ContributionFrequency::Yearly => max(0, (int) $start->diffInYears($end)),
            default => 0,
        };
    }
}
