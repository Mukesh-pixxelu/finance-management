<?php

namespace App\Http\Controllers;

use App\Bank;
use App\ContributionFrequency;
use App\Models\Pension;
use App\PensionScheme;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PensionController extends Controller
{
    public function index(Request $request): View
    {
        $pensions = $request->user()->pensions()
            ->orderBy('start_date')
            ->orderBy('id')
            ->get();

        $yearlyContribution = $pensions->reduce(
            fn (string $carry, Pension $pension) => bcadd($carry, $pension->yearlyContribution(), 2),
            '0.00',
        );

        $expectedPension = $pensions->reduce(
            fn (string $carry, Pension $pension) => bcadd($carry, (string) ($pension->expected_pension ?? '0'), 2),
            '0.00',
        );

        $contributed = $pensions->reduce(
            fn (string $carry, Pension $pension) => bcadd($carry, $pension->totalContributedSoFar(), 2),
            '0.00',
        );

        return view('pensions.index', [
            'pensions' => $pensions,
            'totals' => [
                'yearly_contribution' => $yearlyContribution,
                'expected_pension' => $expectedPension,
                'contributed' => $contributed,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->pensions()->create($this->validated($request));

        return redirect()->route('pensions.index');
    }

    public function edit(Pension $pension): View
    {
        Gate::authorize('update', $pension);

        return view('pensions.edit', [
            'pension' => $pension,
        ]);
    }

    public function update(Request $request, Pension $pension): RedirectResponse
    {
        Gate::authorize('update', $pension);

        $pension->update($this->validated($request, $pension));

        return redirect()->route('pensions.index');
    }

    public function destroy(Pension $pension): RedirectResponse
    {
        Gate::authorize('delete', $pension);

        $pension->delete();

        return redirect()->route('pensions.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Pension $pension = null): array
    {
        return $request->validate([
            'scheme' => ['required', Rule::enum(PensionScheme::class)],
            'bank_name' => [
                'required',
                'string',
                Rule::in(array_values(array_unique([
                    ...Bank::options(),
                    ...($pension?->bank_name ? [$pension->bank_name] : []),
                ]))),
            ],
            'pran' => [
                'required',
                'string',
                'max:40',
                'regex:/^[0-9]+$/',
                Rule::unique('pensions', 'pran')
                    ->where('user_id', $request->user()->id)
                    ->ignore($pension),
            ],
            'contribution_amount' => ['required', 'numeric', 'min:0.01', 'decimal:0,2', 'max:99999999.99'],
            'contribution_frequency' => ['required', Rule::enum(ContributionFrequency::class)],
            'start_date' => ['required', 'date'],
            'pension_start_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'expected_pension' => ['nullable', 'numeric', 'min:0', 'decimal:0,2', 'max:99999999.99'],
            'expected_corpus' => ['nullable', 'numeric', 'min:0', 'decimal:0,2', 'max:9999999999.99'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
