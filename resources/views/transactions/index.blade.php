@php
    use App\Support\Money;
@endphp

@extends('layouts.app')

@section('title', 'Transactions')

@section('shell', 'shell-wide')

@section('content')
    @include('partials.header')

    <div class="hello">
        <h1>Hello, {{ auth()->user()->name }}</h1>
        <p class="lede">Income minus expenses.</p>
    </div>

    <section class="stats stats-dash">
        <article @class(['card', 'stat', 'stat-balance', 'is-negative' => str_starts_with($summary['balance'], '-')])>
            <span class="stat-label"><x-icon name="wallet" /> Balance</span>
            <strong>{{ Money::indian($summary['balance']) }}</strong>
        </article>
        <article class="card stat stat-income">
            <span class="stat-label"><x-icon name="income" class="money-in" /> Income</span>
            <strong @class(['money-in' => $summary['income'] !== '0.00'])>{{ Money::indian($summary['income']) }}</strong>
        </article>
        <article class="card stat stat-expense">
            <span class="stat-label"><x-icon name="expense" class="money-out" /> Expenses</span>
            <strong @class(['money-out' => $summary['expense'] !== '0.00'])>{{ Money::indian($summary['expense']) }}</strong>
        </article>
    </section>

    <div class="board">
        <section class="card panel">
            <div class="panel-head">
                <h2><x-icon name="receipt" /> Transactions</h2>
                <span class="muted">{{ $transactions->count() }}</span>
            </div>

            @if ($transactions->isEmpty())
                <p class="empty"><x-icon name="receipt" class="icon-lg" /> No transactions yet.</p>
            @else
                <ul class="ledger">
                    @foreach ($transactions as $transaction)
                        <li>
                            <div class="txn-main">
                                <strong>{{ $transaction->description }}</strong>
                                <div class="muted">{{ $transaction->occurred_on->toDateString() }}</div>
                                <span class="badge badge-{{ $transaction->type->value }}"><x-icon name="{{ $transaction->type->value }}" /> {{ $transaction->type->value }}</span>
                            </div>
                            <strong @class([
                                'money',
                                'money-in' => $transaction->type === \App\TransactionType::Income,
                                'money-out' => $transaction->type === \App\TransactionType::Expense,
                            ])>{{ $transaction->type === \App\TransactionType::Income ? '+' : '−' }}{{ Money::indian($transaction->amount) }}</strong>
                            <form method="POST" action="{{ route('transactions.destroy', $transaction) }}">
                                @csrf
                                @method('DELETE')
                                <button class="button-text" type="submit"><x-icon name="trash" /> Delete</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="card panel panel-form">
            <h2><x-icon name="plus" /> Add a transaction</h2>

            <form method="POST" action="{{ route('transactions.store') }}">
                @csrf

                <div class="fields">
                    <div>
                        <label for="type">Type</label>
                        <select id="type" name="type" required>
                            <option value="income" @selected(old('type') === 'income')>Income</option>
                            <option value="expense" @selected(old('type', 'expense') === 'expense')>Expense</option>
                        </select>
                        @error('type') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="amount">Amount</label>
                        <input id="amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount') }}" required>
                        @error('amount') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="wide">
                        <label for="description">Description</label>
                        <input id="description" name="description" value="{{ old('description') }}" required>
                        @error('description') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="occurred_on">Date</label>
                        <input id="occurred_on" name="occurred_on" type="date" value="{{ old('occurred_on', now()->toDateString()) }}" required>
                        @error('occurred_on') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="wide">
                        <button class="button" type="submit"><x-icon name="plus" /> Save</button>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection
