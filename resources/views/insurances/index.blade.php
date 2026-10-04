@extends('layouts.app')

@section('title', 'Insurance')

@section('shell', 'shell-savings')

@section('content')
    @include('partials.header')

    <h1><x-icon name="shield" class="icon-lg" /> Insurance</h1>
    <p class="lede">Life, health, accident (PMSBY), vehicle, and other policies you already hold.</p>

    <section class="card card-form">
        <h2><x-icon name="plus" /> Add a policy</h2>

        <form method="POST" action="{{ route('insurances.store') }}">
            @csrf
            @include('insurances.partials.form-fields')
            <p class="hint form-hint">Track cover, premium, and renewal dates in one place.</p>
            <div class="form-submit">
                <button class="button" type="submit"><x-icon name="plus" /> Save</button>
            </div>
        </form>
    </section>

    <section class="savings-section">
        <div class="savings-heading">
            <h2><x-icon name="shield" /> Your policies</h2>
            <p class="savings-totals">
                <span>Total cover: <strong>{{ \App\Support\Money::indian($totals['cover']) }}</strong></span>
                <span>Yearly premium: <strong>{{ \App\Support\Money::indian($totals['yearly_premium']) }}</strong></span>
            </p>
        </div>

        @if ($insurances->isEmpty())
            <p class="empty"><x-icon name="shield" class="icon-lg" /> No policies yet.</p>
        @else
            <div class="saving-list">
                @foreach ($insurances as $insurance)
                    <article class="card saving">
                        <div class="saving-head">
                            <span class="badge badge-{{ $insurance->type->value }}">
                                <x-icon name="shield" />
                                {{ $insurance->type->label() }}
                            </span>

                            <div class="saving-actions">
                                <a class="button-quiet" href="{{ route('insurances.edit', $insurance) }}"><x-icon name="pencil" /> Edit</a>
                                <form method="POST" action="{{ route('insurances.destroy', $insurance) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button-quiet button-danger" type="submit"><x-icon name="trash" /> Delete</button>
                                </form>
                            </div>
                        </div>

                        <div class="saving-identity">
                            <strong class="account-number">{{ $insurance->policy_number }}</strong>
                            <span class="bank-name">{{ $insurance->provider }}</span>
                        </div>

                        <div class="saving-strip">
                            <span>Sum assured</span>
                            <strong>{{ \App\Support\Money::indian($insurance->sum_assured) }}</strong>
                        </div>

                        <dl class="facts facts-bordered">
                            <div>
                                <dt><x-icon name="landmark" /> Bank</dt>
                                <dd>{{ filled($insurance->bank_name) ? $insurance->bank_name : '—' }}</dd>
                            </div>
                            <div>
                                <dt><x-icon name="user" /> Insured</dt>
                                <dd>{{ $insurance->insured_person }}</dd>
                            </div>
                            <div>
                                <dt><x-icon name="coins" /> Premium</dt>
                                <dd>{{ \App\Support\Money::indian($insurance->premium_amount) }} / {{ strtolower($insurance->premium_frequency->label()) }}</dd>
                            </div>
                            <div>
                                <dt><x-icon name="calendar" /> Start date</dt>
                                <dd>{{ $insurance->start_date?->format('d M Y') ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt><x-icon name="calendar" /> Expiry</dt>
                                <dd>{{ $insurance->expiry_date?->format('d M Y') ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt><x-icon name="receipt" /> Yearly cost</dt>
                                <dd>{{ \App\Support\Money::indian($insurance->yearlyPremium()) }}</dd>
                            </div>
                            @if (filled($insurance->notes))
                                <div>
                                    <dt><x-icon name="pencil" /> Notes</dt>
                                    <dd>{{ $insurance->notes }}</dd>
                                </div>
                            @endif
                        </dl>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
