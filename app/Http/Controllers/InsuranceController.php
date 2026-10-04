<?php

namespace App\Http\Controllers;

use App\Bank;
use App\InsuranceType;
use App\Insurer;
use App\Models\Insurance;
use App\PremiumFrequency;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class InsuranceController extends Controller
{
    public function index(Request $request): View
    {
        $insurances = $request->user()->insurances()
            ->orderBy('expiry_date')
            ->orderBy('id')
            ->get();

        $totalCover = $insurances->reduce(
            fn (string $carry, Insurance $insurance) => bcadd($carry, (string) ($insurance->sum_assured ?? '0'), 2),
            '0.00',
        );

        $yearlyPremium = $insurances->reduce(
            fn (string $carry, Insurance $insurance) => bcadd($carry, $insurance->yearlyPremium(), 2),
            '0.00',
        );

        return view('insurances.index', [
            'insurances' => $insurances,
            'totals' => [
                'cover' => $totalCover,
                'yearly_premium' => $yearlyPremium,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->insurances()->create($this->validated($request));

        return redirect()->route('insurances.index');
    }

    public function edit(Insurance $insurance): View
    {
        Gate::authorize('update', $insurance);

        return view('insurances.edit', [
            'insurance' => $insurance,
        ]);
    }

    public function update(Request $request, Insurance $insurance): RedirectResponse
    {
        Gate::authorize('update', $insurance);

        $insurance->update($this->validated($request, $insurance));

        return redirect()->route('insurances.index');
    }

    public function destroy(Insurance $insurance): RedirectResponse
    {
        Gate::authorize('delete', $insurance);

        $insurance->delete();

        return redirect()->route('insurances.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Insurance $insurance = null): array
    {
        return $request->validate([
            'type' => ['required', Rule::enum(InsuranceType::class)],
            'provider' => [
                'required',
                'string',
                Rule::in(array_values(array_unique([
                    ...Insurer::options(),
                    ...($insurance?->provider ? [$insurance->provider] : []),
                ]))),
            ],
            'bank_name' => [
                'required',
                'string',
                Rule::in(array_values(array_unique([
                    ...Bank::options(),
                    ...($insurance?->bank_name ? [$insurance->bank_name] : []),
                ]))),
            ],
            'policy_number' => [
                'required',
                'string',
                'max:60',
                'regex:/^[A-Za-z0-9\\-\\/]+$/',
                Rule::unique('insurances', 'policy_number')
                    ->where('user_id', $request->user()->id)
                    ->ignore($insurance),
            ],
            'insured_person' => ['required', 'string', 'max:120'],
            'sum_assured' => ['required', 'numeric', 'min:0.01', 'decimal:0,2', 'max:9999999999.99'],
            'premium_amount' => ['required', 'numeric', 'min:0.01', 'decimal:0,2', 'max:99999999.99'],
            'premium_frequency' => ['required', Rule::enum(PremiumFrequency::class)],
            'start_date' => ['required', 'date'],
            'expiry_date' => ['required', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
