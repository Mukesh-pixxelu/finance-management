@extends('layouts.app')

@section('title', 'Pension')

@section('shell', 'shell-savings')

@section('content')
    @include('partials.header')

    <h1><x-icon name="coins" class="icon-lg" /> Pension</h1>
    <p class="lede">Atal Pension Yojana, NPS, and other retirement contributions.</p>

    <section class="card card-form">
        <h2><x-icon name="plus" /> Add a pension</h2>

        <form method="POST" action="{{ route('pensions.store') }}">
            @csrf
            @include('pensions.partials.form-fields')
            <p class="hint form-hint">Track contribution start, pension start date, PRAN, and expected pension or corpus.</p>
            <div class="form-submit">
                <button class="button" type="submit"><x-icon name="plus" /> Save</button>
            </div>
        </form>
    </section>

    <section class="savings-section">
        <div class="savings-heading">
            <h2><x-icon name="coins" /> Your pensions</h2>
            <p class="savings-totals">
                <span>Contributed so far: <strong>{{ \App\Support\Money::indian($totals['contributed']) }}</strong></span>
                <span>Yearly contribution: <strong>{{ \App\Support\Money::indian($totals['yearly_contribution']) }}</strong></span>
                <span>Expected monthly pension: <strong>{{ \App\Support\Money::indian($totals['expected_pension']) }}</strong></span>
            </p>
        </div>

        @if ($pensions->isEmpty())
            <p class="empty"><x-icon name="coins" class="icon-lg" /> No pension accounts yet.</p>
        @else
            <div class="saving-list">
                @foreach ($pensions as $pension)
                    <article class="card saving">
                        <div class="saving-head">
                            <span class="badge badge-{{ $pension->scheme->value }}">
                                <x-icon name="coins" />
                                {{ $pension->scheme->label() }}
                            </span>

                            <div class="saving-actions">
                                <a class="button-quiet" href="{{ route('pensions.edit', $pension) }}"><x-icon name="pencil" /> Edit</a>
                                <form method="POST" action="{{ route('pensions.destroy', $pension) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button-quiet button-danger" type="submit"><x-icon name="trash" /> Delete</button>
                                </form>
                            </div>
                        </div>

                        <div class="saving-identity">
                            <strong class="account-number">{{ $pension->pran }}</strong>
                            <span class="bank-name">{{ $pension->bank_name }}</span>
                        </div>

                        <div class="saving-strip">
                            <span>Contribution</span>
                            <strong>{{ \App\Support\Money::indian($pension->contribution_amount) }} / {{ strtolower($pension->contribution_frequency->label()) }}</strong>
                        </div>

                        @php
                            $paid = $pension->contributionsSoFar();
                            $totalPremiums = $pension->totalContributions();
                            $remaining = $pension->contributionsRemaining();
                            $progress = $pension->contributionProgressPercent();
                            $contributed = $pension->totalContributedSoFar();
                        @endphp

                        <div class="pension-progress">
                            <div class="pension-progress-head">
                                <div>
                                    <span class="overview-label">Total contributed so far</span>
                                    <strong class="pension-contributed">{{ \App\Support\Money::indian($contributed) }}</strong>
                                </div>
                                <div class="pension-progress-counts">
                                    @if ($totalPremiums > 0)
                                        <strong>{{ $paid }}/{{ $totalPremiums }}</strong>
                                        <span>premiums paid</span>
                                    @else
                                        <strong>{{ $paid }}</strong>
                                        <span>premiums paid</span>
                                    @endif
                                </div>
                            </div>

                            @if ($totalPremiums > 0)
                                <div
                                    class="progress-track"
                                    role="progressbar"
                                    aria-valuemin="0"
                                    aria-valuemax="100"
                                    aria-valuenow="{{ $progress }}"
                                    aria-label="Contribution progress"
                                >
                                    <span class="progress-fill" style="width: {{ $progress }}%"></span>
                                </div>
                                <div class="pension-progress-meta">
                                    <span>{{ number_format($progress, 1) }}% complete</span>
                                    <span>{{ $remaining }} {{ Str::plural('premium', $remaining) }} left</span>
                                </div>
                            @else
                                <p class="hint">Add pension start date to see remaining premiums and progress.</p>
                            @endif
                        </div>

                        <dl class="facts facts-bordered">
                            <div>
                                <dt><x-icon name="calendar" /> Contribution start</dt>
                                <dd>{{ $pension->start_date?->format('d M Y') ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt><x-icon name="calendar" /> Pension start</dt>
                                <dd>{{ $pension->pension_start_date?->format('d M Y') ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt><x-icon name="receipt" /> Yearly cost</dt>
                                <dd>{{ \App\Support\Money::indian($pension->yearlyContribution()) }}</dd>
                            </div>
                            <div>
                                <dt><x-icon name="coins" /> Expected pension</dt>
                                <dd>
                                    @if ($pension->expected_pension !== null)
                                        {{ \App\Support\Money::indian($pension->expected_pension) }} / month
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt><x-icon name="landmark" /> Expected corpus</dt>
                                <dd>
                                    @if ($pension->expected_corpus !== null)
                                        {{ \App\Support\Money::indian($pension->expected_corpus) }}
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                            @if (filled($pension->notes))
                                <div>
                                    <dt><x-icon name="pencil" /> Notes</dt>
                                    <dd>{{ $pension->notes }}</dd>
                                </div>
                            @endif
                        </dl>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
