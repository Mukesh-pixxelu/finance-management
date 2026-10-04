@php
    use App\Support\Money;
@endphp

@extends('layouts.app')

@section('title', $bankName)

@section('shell', 'shell-savings')

@section('content')
    @include('partials.header')

    <div class="hello">
        <p class="muted"><a href="{{ route('dashboard') }}">← Back to dashboard</a></p>
        <h1><x-icon name="landmark" class="icon-lg" /> {{ $bankName }}</h1>
        <p class="lede">{{ $count }} {{ Str::plural('account', $count) }} · Total {{ Money::indian($total) }}</p>
    </div>

    <div class="saving-list">
        @foreach ($savings as $saving)
            <article class="card saving">
                <div class="saving-head">
                    <span class="badge badge-{{ $saving->type->value }}">
                        <x-icon name="{{ match ($saving->type) {
                            \App\SavingType::SavingsAccount => 'piggy',
                            \App\SavingType::FixedDeposit => 'landmark',
                            \App\SavingType::RecurringDeposit => 'repeat',
                        } }}" />
                        {{ $saving->type->label() }}
                    </span>

                    <div class="saving-actions">
                        <a class="button-quiet" href="{{ route('savings.edit', $saving) }}"><x-icon name="pencil" /> Edit</a>
                        <form method="POST" action="{{ route('savings.destroy', $saving) }}">
                            @csrf
                            @method('DELETE')
                            <button class="button-quiet button-danger" type="submit"><x-icon name="trash" /> Delete</button>
                        </form>
                    </div>
                </div>

                <div class="saving-identity">
                    <strong class="account-number">{{ $saving->account_number }}</strong>
                    <span class="bank-name">{{ $saving->bank_name }}</span>
                </div>

                <div class="saving-strip">
                    <span>{{ $saving->type->amountLabel() }}</span>
                    <strong>{{ Money::indian($saving->amount) }}</strong>
                </div>

                <dl class="facts facts-bordered">
                    <div>
                        <dt><x-icon name="percent" /> Interest rate</dt>
                        <dd>{{ number_format((float) $saving->interest_rate, 2) }}%</dd>
                    </div>
                    <div>
                        <dt><x-icon name="calendar" /> Start date</dt>
                        <dd>{{ $saving->start_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt><x-icon name="calendar" /> Maturity date</dt>
                        <dd>{{ $saving->maturity_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt><x-icon name="arrow-up" /> Monthly return</dt>
                        <dd class="money-in">{{ Money::indian($saving->interest_earned) }}</dd>
                    </div>
                    <div>
                        <dt><x-icon name="coins" /> Value at maturity</dt>
                        <dd>{{ Money::indian($saving->receivableAtMaturity()) }}</dd>
                    </div>
                </dl>
            </article>
        @endforeach
    </div>
@endsection
