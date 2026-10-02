@extends('layouts.app')

@section('title', 'Register')

@section('shell', 'shell-narrow')

@section('content')
    <p class="brand"><x-icon name="wallet" /> Ledger</p>
    <h1>Create account</h1>
    <p class="lede">Start with a name and a password.</p>

    <form class="card form-card" method="POST" action="{{ route('register.store') }}">
        @csrf

        <div class="fields">
            <div class="wide">
                <label for="name">Name</label>
                <input id="name" name="name" value="{{ old('name') }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

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
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required>
            </div>

            <div class="wide">
                <button class="button" type="submit">Register</button>
            </div>
        </div>
    </form>

    <p class="auth-links"><a href="{{ route('login') }}">Login</a></p>
@endsection