<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $summary = $user->ledgerSummary();

        return view('dashboard', [
            'transactions' => $user->transactions()
                ->orderByDesc('occurred_on')
                ->orderByDesc('id')
                ->get(),
            'summary' => $summary,
            'chart' => $this->chart($summary['income'], $summary['expense']),
        ]);
    }

    /**
     * @return array{empty: bool, income: string, expense: string}
     */
    private function chart(string $income, string $expense): array
    {
        $total = bcadd($income, $expense, 2);

        if (bccomp($total, '0', 2) !== 1) {
            return [
                'empty' => true,
                'income' => '0.0',
                'expense' => '0.0',
            ];
        }

        $incomeShare = number_format(round((float) $income / (float) $total * 100, 1), 1, '.', '');

        return [
            'empty' => false,
            'income' => $incomeShare,
            'expense' => number_format(round(100 - (float) $incomeShare, 1), 1, '.', ''),
        ];
    }
}
