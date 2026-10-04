@extends('layouts.app')

@section('title', 'Dashboard')

@section('shell', 'shell-wide')

@section('content')
    @include('partials.header')

    <div class="hello">
        <h1>Hello, {{ auth()->user()->name }}</h1>
        <p class="lede">Your banks, insurance cover, and pension contributions.</p>
    </div>

    <section class="card assets-hero">
        <div class="assets-summary">
            <span class="stat-label"><x-icon name="wallet" /> Total assets</span>
            <strong class="assets-total">{{ \App\Support\Money::indian($totalAssets) }}</strong>
            <p class="muted">{{ $banks->count() }} {{ Str::plural('bank', $banks->count()) }} · {{ $banks->sum('count') }} {{ Str::plural('account', $banks->sum('count')) }}</p>
        </div>

        <div class="assets-chart">
            <div
                @class(['pie', 'pie-assets', 'pie-empty' => $banks->isEmpty()])
                @unless ($banks->isEmpty()) style="background: radial-gradient(circle at center, #fff 0 52%, transparent 53%), {{ $pieStyle }}" @endunless
                role="img"
                aria-label="Bank wise asset share"
            ></div>

            @if ($banks->isEmpty())
                <p class="hint">Add savings to see bank-wise assets.</p>
            @else
                <ul class="legend">
                    @foreach ($banks as $bank)
                        <li>
                            <span class="swatch" style="background: {{ $bank['color'] }}"></span>
                            <span>{{ $bank['name'] }}</span>
                            <strong>{{ $bank['percent'] }}%</strong>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    <section class="overview-section">
        <h2><x-icon name="pie" /> Cover &amp; retirement</h2>
        <div class="overview-grid">
            <a class="card overview-card" href="{{ route('insurances.index') }}">
                <div class="overview-head">
                    <span class="overview-icon"><x-icon name="shield" /></span>
                    <div>
                        <strong class="overview-title">Insurance</strong>
                        <span class="overview-subtitle">
                            @if ($insurance['count'] === 0)
                                No policies yet
                            @else
                                {{ $insurance['count'] }} {{ Str::plural('policy', $insurance['count']) }}
                            @endif
                        </span>
                    </div>
                </div>

                @if ($insurance['count'] === 0)
                    <p class="overview-empty">Track life, health, and vehicle cover in one place.</p>
                    <span class="overview-link">Add a policy →</span>
                @else
                    <div class="overview-metric">
                        <span class="overview-label">Total cover</span>
                        <strong class="overview-amount">{{ \App\Support\Money::indian($insurance['cover']) }}</strong>
                    </div>
                    <div class="overview-stats">
                        <div>
                            <span class="overview-label">Yearly premium</span>
                            <strong>{{ \App\Support\Money::indian($insurance['yearly_premium']) }}</strong>
                        </div>
                        <div>
                            <span class="overview-label">Policies</span>
                            <strong>{{ $insurance['count'] }}</strong>
                        </div>
                    </div>
                @endif
            </a>

            <a class="card overview-card overview-card-pension" href="{{ route('pensions.index') }}">
                <div class="overview-head">
                    <span class="overview-icon"><x-icon name="coins" /></span>
                    <div>
                        <strong class="overview-title">Pension</strong>
                        <span class="overview-subtitle">
                            @if ($pension['count'] === 0)
                                No accounts yet
                            @else
                                {{ $pension['count'] }} {{ Str::plural('account', $pension['count']) }}
                            @endif
                        </span>
                    </div>
                </div>

                @if ($pension['count'] === 0)
                    <p class="overview-empty">Track APY / NPS contributions and expected pension.</p>
                    <span class="overview-link">Add a pension →</span>
                @else
                    <div class="overview-metric">
                        <span class="overview-label">Contributed so far</span>
                        <strong class="overview-amount">{{ \App\Support\Money::indian($pension['contributed']) }}</strong>
                        <span class="overview-count">{{ $pension['contributions'] }} {{ Str::plural('contribution', $pension['contributions']) }}</span>
                    </div>
                    <div class="overview-stats">
                        <div>
                            <span class="overview-label">Yearly</span>
                            <strong>{{ \App\Support\Money::indian($pension['yearly_contribution']) }}</strong>
                        </div>
                        <div>
                            <span class="overview-label">Expected / mo</span>
                            <strong>{{ \App\Support\Money::indian($pension['expected_pension']) }}</strong>
                        </div>
                    </div>
                @endif
            </a>
        </div>
    </section>

    <section class="bank-section">
        <h2><x-icon name="landmark" /> Banks</h2>

        @if ($banks->isEmpty())
            <p class="empty"><x-icon name="landmark" class="icon-lg" /> No bank assets yet. <a href="{{ route('savings.index') }}">Add a saving</a></p>
        @else
            <div class="bank-grid">
                @foreach ($banks as $bank)
                    <a class="card bank-card" href="{{ route('banks.show', ['bank' => $bank['slug']]) }}">
                        <span class="bank-card-swatch" style="background: {{ $bank['color'] }}"></span>
                        <strong class="bank-card-name">{{ $bank['name'] }}</strong>
                        <span class="bank-card-amount">{{ \App\Support\Money::indian($bank['total']) }}</span>
                        <span class="muted">{{ $bank['count'] }} {{ Str::plural('account', $bank['count']) }} · {{ $bank['percent'] }}%</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
@endsection
