<?php

namespace Tests\Unit\Models;

use App\ContributionFrequency;
use App\Models\Pension;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PensionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_contributions_so_far_uses_frequency_and_start_date(): void
    {
        Carbon::setTestNow('2026-10-04');

        $pension = Pension::factory()->make([
            'contribution_amount' => '1000.00',
            'contribution_frequency' => ContributionFrequency::Monthly,
            'start_date' => '2026-05-01',
            'pension_start_date' => '2046-05-01',
        ]);

        $this->assertSame(6, $pension->contributionsSoFar());
        $this->assertSame('6000.00', $pension->totalContributedSoFar());
        $this->assertSame(240, $pension->totalContributions());
        $this->assertSame(234, $pension->contributionsRemaining());
        $this->assertSame(2.5, $pension->contributionProgressPercent());

        Carbon::setTestNow();
    }
}
