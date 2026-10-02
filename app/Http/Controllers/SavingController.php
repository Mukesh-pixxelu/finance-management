<?php

namespace App\Http\Controllers;

use App\Models\Saving;
use App\SavingType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SavingController extends Controller
{
    public function index(Request $request): View
    {
        return view('savings.index', [
            'savings' => $request->user()->savings()
                ->orderBy('maturity_date')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::enum(SavingType::class)],
            'account_number' => [
                'required',
                'string',
                'max:40',
                Rule::unique('savings', 'account_number')->where('user_id', $request->user()->id),
            ],
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:100', 'decimal:0,2'],
            'amount' => ['required', 'numeric', 'min:0.01', 'decimal:0,2', 'max:99999999.99'],
            'start_date' => ['required', 'date'],
            'maturity_date' => ['required', 'date', 'after_or_equal:start_date'],
            'interest_earned' => ['required', 'numeric', 'min:0', 'decimal:0,2', 'max:99999999.99'],
        ]);

        $request->user()->savings()->create([
            'type' => $data['type'],
            'account_number' => $data['account_number'],
            'interest_rate' => $data['interest_rate'],
            'amount' => $data['amount'],
            'start_date' => $data['start_date'],
            'maturity_date' => $data['maturity_date'],
            'interest_earned' => $data['interest_earned'],
        ]);

        return redirect()->route('savings.index');
    }

    public function destroy(Saving $saving): RedirectResponse
    {
        Gate::authorize('delete', $saving);

        $saving->delete();

        return redirect()->route('savings.index');
    }
}
