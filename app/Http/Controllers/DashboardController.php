<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $banks = $this->bankAssets($request->user());
        $totalAssets = $banks->reduce(
            fn (string $carry, array $bank) => bcadd($carry, $bank['total'], 2),
            '0.00',
        );

        return view('dashboard', [
            'totalAssets' => $totalAssets,
            'banks' => $banks,
            'pieStyle' => $this->pieStyle($banks),
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
