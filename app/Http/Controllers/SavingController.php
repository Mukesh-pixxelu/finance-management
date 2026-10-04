<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Models\Saving;
use App\SavingType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SavingController extends Controller
{
    public function index(Request $request): View
    {
        $savings = $request->user()->savings()
            ->orderBy('maturity_date')
            ->orderBy('id')
            ->get();

        return view('savings.index', [
            'savings' => $savings,
            'totals' => [
                'balance' => $this->totalFor($savings, SavingType::SavingsAccount),
                'principal' => $this->totalFor($savings, SavingType::FixedDeposit),
                'installment' => $this->totalFor($savings, SavingType::RecurringDeposit),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, extended: false);

        $start = Carbon::parse($data['start_date']);
        $rate = (float) $data['interest_rate'];
        $amount = (float) $data['amount'];

        $request->user()->savings()->create([
            ...$data,
            'maturity_date' => $start->copy()->addYear()->toDateString(),
            'interest_earned' => number_format($amount * ($rate / 100), 2, '.', ''),
        ]);

        return redirect()->route('savings.index');
    }

    public function edit(Saving $saving): View
    {
        Gate::authorize('update', $saving);

        return view('savings.edit', [
            'saving' => $saving,
        ]);
    }

    public function update(Request $request, Saving $saving): RedirectResponse
    {
        Gate::authorize('update', $saving);

        $saving->update($this->validated($request, extended: true, saving: $saving));

        return redirect()->route('savings.index');
    }

    public function destroy(Saving $saving): RedirectResponse
    {
        Gate::authorize('delete', $saving);

        $saving->delete();

        return redirect()->route('savings.index');
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Saving>  $savings
     */
    private function totalFor($savings, SavingType $type): string
    {
        $total = $savings
            ->filter(fn (Saving $saving) => $saving->type === $type)
            ->reduce(fn (string $carry, Saving $saving) => bcadd($carry, $saving->amount, 2), '0.00');

        return $total;
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, bool $extended, ?Saving $saving = null): array
    {
        $rules = [
            'type' => ['required', Rule::enum(SavingType::class)],
            'account_number' => [
                'required',
                'string',
                'max:40',
                'regex:/^[0-9]+$/',
                Rule::unique('savings', 'account_number')
                    ->where('user_id', $request->user()->id)
                    ->ignore($saving),
            ],
            'bank_name' => [
                'required',
                'string',
                Rule::in(array_values(array_unique([
                    ...Bank::options(),
                    ...($saving?->bank_name ? [$saving->bank_name] : []),
                ]))),
            ],
            'interest_rate' => ['required', 'numeric', 'min:1', 'max:20', 'decimal:0,2'],
            'amount' => ['required', 'numeric', 'min:0.01', 'decimal:0,2', 'max:99999999.99'],
            'start_date' => ['required', 'date'],
        ];

        if ($extended) {
            $rules['maturity_date'] = ['required', 'date', 'after_or_equal:start_date'];
            $rules['interest_earned'] = ['required', 'numeric', 'min:0', 'decimal:0,2', 'max:99999999.99'];
        }

        return $request->validate($rules);
    }
}
