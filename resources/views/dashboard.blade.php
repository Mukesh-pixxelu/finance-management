@php
    use App\Support\Money;
@endphp

@extends('layouts.app')

@section('title', 'Dashboard')

@section('shell', 'shell-wide')

@section('content')
    @include('partials.header')

    <div class="hello">
        <h1>Hello, {{ auth()->user()->name }}</h1>
        <p class="lede">Your total assets across banks.</p>
    </div>

    <section class="card assets-hero">
        <div class="assets-summary">
            <span class="stat-label"><x-icon name="wallet" /> Total assets</span>
            <strong class="assets-total">{{ Money::indian($totalAssets) }}</strong>
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
                        <span class="bank-card-amount">{{ Money::indian($bank['total']) }}</span>
                        <span class="muted">{{ $bank['count'] }} {{ Str::plural('account', $bank['count']) }} · {{ $bank['percent'] }}%</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
@endsection
