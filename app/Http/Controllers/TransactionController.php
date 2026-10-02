<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\TransactionType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::enum(TransactionType::class)],
            'amount' => ['required', 'numeric', 'min:0.01', 'decimal:0,2', 'max:99999999.99'],
            'description' => ['required', 'string', 'max:255'],
            'occurred_on' => ['required', 'date'],
        ]);

        $request->user()->transactions()->create([
            'type' => $data['type'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'occurred_on' => $data['occurred_on'],
        ]);

        return redirect()->route('dashboard');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        Gate::authorize('delete', $transaction);

        $transaction->delete();

        return redirect()->route('dashboard');
    }
}
