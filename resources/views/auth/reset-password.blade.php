@extends('layouts.app')

@section('title', 'Reset password')

@section('shell', 'shell-narrow')

@section('content')
    <p class="brand"><x-icon name="wallet" /> Ledger</p>
    <h1>Choose a new password</h1>
    <p class="lede">Use at least 8 characters.</p>

    <form class="card form-card" method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="fields">
            <div class="wide">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="wide">
                <label for="password">New password</label>
                <input id="password" name="password" type="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="wide">
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required>
            </div>

            <div class="wide">
                <button class="button" type="submit">Reset password</button>
            </div>
        </div>
    </form>
@endsection
