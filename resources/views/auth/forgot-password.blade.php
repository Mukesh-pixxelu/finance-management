@extends('layouts.app')

@section('title', 'Forgot password')

@section('shell', 'shell-narrow')

@section('content')
    <p class="brand"><x-icon name="wallet" /> Ledger</p>
    <h1>Forgot password</h1>
    <p class="lede">We will send a reset link to your email.</p>

    @if (session('status'))
        <p class="status">{{ session('status') }}</p>
    @endif

    <form class="card form-card" method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="fields">
            <div class="wide">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="wide">
                <button class="button" type="submit">Send reset link</button>
            </div>
        </div>
    </form>

    <p class="auth-links"><a href="{{ route('login') }}">Back to login</a></p>
@endsection
