@extends('layouts.app')

@section('title', 'Login')

@section('shell', 'shell-narrow')

@section('content')
    <p class="brand"><x-icon name="wallet" /> Ledger</p>
    <h1>Login</h1>
    <p class="lede">Your money, in one place.</p>

    <form class="card form-card" method="POST" action="{{ route('login.store') }}">
        @csrf

        <div class="fields">
            <div class="wide">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="wide">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="wide">
                <button class="button" type="submit">Login</button>
            </div>
        </div>
    </form>

    <p class="auth-links">
        <a href="{{ route('password.request') }}">Forgot password?</a>
        <a href="{{ route('register') }}">Naya account</a>
    </p>
@endsection
