<?php

namespace App\Http\Controllers;

use App\Bank;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function show(Request $request, string $bank): View
    {
        $user = $request->user();
        $bankName = Bank::nameFromSlug($bank)
            ?? $user->savings()
                ->pluck('bank_name')
                ->filter(fn ($name) => filled($name))
                ->unique()
                ->map(fn ($name) => (string) $name)
                ->first(fn (string $name) => Bank::slug($name) === $bank);

        abort_if($bankName === null, 404);

        $savings = $user->savings()
            ->where('bank_name', $bankName)
            ->orderBy('maturity_date')
            ->orderBy('id')
            ->get();

        abort_if($savings->isEmpty(), 404);

        $total = $savings->reduce(
            fn (string $carry, $saving) => bcadd($carry, (string) ($saving->amount ?? '0'), 2),
            '0.00',
        );

        return view('banks.show', [
            'bankName' => $bankName,
            'savings' => $savings,
            'total' => $total,
            'count' => $savings->count(),
        ]);
    }
}
