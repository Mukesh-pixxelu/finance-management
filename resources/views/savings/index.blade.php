@extends('layouts.app')

@section('title', 'Savings')

@section('shell', 'shell-wide')

@section('content')
    @include('partials.header')

    <h1><x-icon name="piggy" class="icon-lg" /> Savings</h1>
    <p class="lede">Savings accounts, fixed deposits, and recurring deposits.</p>

    <section class="card">
        <h2><x-icon name="plus" /> Add a saving</h2>

        <form method="POST" action="{{ route('savings.store') }}">
            @csrf

            <div class="fields">
                <div>
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="savings_account" @selected(old('type', 'savings_account') === 'savings_account')>Savings account</option>
                        <option value="fd" @selected(old('type') === 'fd')>FD</option>
                        <option value="rd" @selected(old('type') === 'rd')>RD</option>
                    </select>
                    @error('type') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="account_number">Account number</label>
                    <input id="account_number" name="account_number" value="{{ old('account_number') }}" required>
                    @error('account_number') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="interest_rate">Interest rate (%)</label>
                    <input id="interest_rate" name="interest_rate" type="number" min="0" max="100" step="0.01" value="{{ old('interest_rate') }}" required>
                    @error('interest_rate') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="amount">Amount</label>
                    <input id="amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount') }}" required>
                    <p class="hint">Savings: balance. FD: principal. RD: monthly installment.</p>
                    @error('amount') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="start_date">Start date</label>
                    <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}" required>
                    @error('start_date') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="maturity_date">Maturity date</label>
                    <input id="maturity_date" name="maturity_date" type="date" value="{{ old('maturity_date') }}" required>
                    @error('maturity_date') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="wide">
                    <label for="interest_earned">Interest earned at maturity</label>
                    <input id="interest_earned" name="interest_earned" type="number" min="0" step="0.01" value="{{ old('interest_earned') }}" required>
                    @error('interest_earned') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="wide">
                    <button class="button" type="submit"><x-icon name="plus" /> Save</button>
                </div>
            </div>
        </form>
    </section>

    <section>
        <h2><x-icon name="landmark" /> Your savings</h2>

        @if ($savings->isEmpty())
            <p class="empty"><x-icon name="piggy" class="icon-lg" /> No savings yet.</p>
        @else
            <div class="saving-list">
                @foreach ($savings as $saving)
                    <article class="card saving">
                        <div class="saving-top">
                            <div>
                                <span class="badge badge-{{ $saving->type->value }}">
                                    <x-icon name="{{ match ($saving->type) {
                                        \App\SavingType::SavingsAccount => 'piggy',
                                        \App\SavingType::FixedDeposit => 'landmark',
                                        \App\SavingType::RecurringDeposit => 'repeat',
                                    } }}" />
                                    {{ $saving->type->label() }}
                                </span>
                                <strong class="account-number">{{ $saving->account_number }}</strong>
                            </div>
                            <form method="POST" action="{{ route('savings.destroy', $saving) }}">
                                @csrf
                                @method('DELETE')
                                <button class="button-text" type="submit"><x-icon name="trash" /> Delete</button>
                            </form>
                        </div>

                        <dl class="facts">
                            <div>
                                <dt><x-icon name="percent" /> Interest rate</dt>
                                <dd>{{ $saving->interest_rate }}%</dd>
                            </div>
                            <div>
                                <dt><x-icon name="coins" /> {{ $saving->type->amountLabel() }}</dt>
                                <dd>{{ $saving->amount }}</dd>
                            </div>
                            <div>
                                <dt><x-icon name="calendar" /> Start date</dt>
                                <dd>{{ $saving->start_date->toDateString() }}</dd>
                            </div>
                            <div>
                                <dt><x-icon name="calendar" /> Maturity date</dt>
                                <dd>{{ $saving->maturity_date->toDateString() }}</dd>
                            </div>
                            <div>
                                <dt><x-icon name="percent" /> Interest at maturity</dt>
                                <dd class="money-in">{{ $saving->interest_earned }}</dd>
                            </div>
                            @if ($receivable = $saving->receivableAtMaturity())
                                <div>
                                    <dt><x-icon name="coins" /> Value at maturity</dt>
                                    <dd>{{ $receivable }}</dd>
                                </div>
                            @endif
                        </dl>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
