<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Models\Insurance;
use App\Models\Pension;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $banks = $this->bankAssets($user);
        $totalAssets = $banks->reduce(
            fn (string $carry, array $bank) => bcadd($carry, $bank['total'], 2),
            '0.00',
        );

        return view('dashboard', [
            'totalAssets' => $totalAssets,
            'banks' => $banks,
            'pieStyle' => $this->pieStyle($banks),
            'insurance' => $this->insuranceSummary($user),
            'pension' => $this->pensionSummary($user),
        ]);
    }

    /**
     * @return Collection<int, array{name: string, slug: string, total: string, count: int, percent: string, color: string}>
     */
    private function bankAssets(User $user): Collection
    {
        $colors = Bank::chartColors();

        $grouped = $user->savings()
            ->selectRaw('bank_name, sum(amount) as total, count(*) as accounts')
            ->groupBy('bank_name')
            ->orderByDesc('total')
            ->get()
            ->filter(fn (object $row) => filled($row->bank_name));

        $grandTotal = $grouped->reduce(
            fn (string $carry, object $row) => bcadd($carry, (string) $row->total, 2),
            '0.00',
        );

        return $grouped->values()->map(function (object $row, int $index) use ($colors, $grandTotal) {
            $name = (string) $row->bank_name;
            $slug = Bank::slug($name);
            $total = bcadd((string) $row->total, '0', 2);
            $percent = bccomp($grandTotal, '0', 2) === 1
                ? number_format(round(((float) $total / (float) $grandTotal) * 100, 1), 1, '.', '')
                : '0.0';

            return [
                'name' => $name,
                'slug' => $slug !== '' ? $slug : 'bank-'.$index,
                'total' => $total,
                'count' => (int) $row->accounts,
                'percent' => $percent,
                'color' => $colors[$index % count($colors)],
            ];
        });
    }

    /**
     * @return array{count: int, cover: string, yearly_premium: string}
     */
    private function insuranceSummary(User $user): array
    {
        $insurances = $user->insurances()->get();

        $cover = $insurances->reduce(
            fn (string $carry, Insurance $insurance) => bcadd($carry, (string) ($insurance->sum_assured ?? '0'), 2),
            '0.00',
        );

        $yearlyPremium = $insurances->reduce(
            fn (string $carry, Insurance $insurance) => bcadd($carry, $insurance->yearlyPremium(), 2),
            '0.00',
        );

        return [
            'count' => $insurances->count(),
            'cover' => $cover,
            'yearly_premium' => $yearlyPremium,
        ];
    }

    /**
     * @return array{count: int, contributions: int, contributed: string, yearly_contribution: string, expected_pension: string}
     */
    private function pensionSummary(User $user): array
    {
        $pensions = $user->pensions()->get();

        $contributions = $pensions->sum(fn (Pension $pension) => $pension->contributionsSoFar());

        $contributed = $pensions->reduce(
            fn (string $carry, Pension $pension) => bcadd($carry, $pension->totalContributedSoFar(), 2),
            '0.00',
        );

        $yearlyContribution = $pensions->reduce(
            fn (string $carry, Pension $pension) => bcadd($carry, $pension->yearlyContribution(), 2),
            '0.00',
        );

        $expectedPension = $pensions->reduce(
            fn (string $carry, Pension $pension) => bcadd($carry, (string) ($pension->expected_pension ?? '0'), 2),
            '0.00',
        );

        return [
            'count' => $pensions->count(),
            'contributions' => (int) $contributions,
            'contributed' => $contributed,
            'yearly_contribution' => $yearlyContribution,
            'expected_pension' => $expectedPension,
        ];
    }

    /**
     * @param  Collection<int, array{percent: string, color: string}>  $banks
     */
    private function pieStyle(Collection $banks): string
    {
        if ($banks->isEmpty()) {
            return '';
        }

        $cursor = 0.0;
        $stops = [];

        foreach ($banks as $bank) {
            $end = min(100, round($cursor + (float) $bank['percent'], 2));
            $stops[] = sprintf('%s %.2f%% %.2f%%', $bank['color'], $cursor, $end);
            $cursor = $end;
        }

        if ($cursor < 100) {
            $stops[] = sprintf('#e2e8f0 %.2f%% 100%%', $cursor);
        }

        return 'conic-gradient('.implode(', ', $stops).')';
    }
}
