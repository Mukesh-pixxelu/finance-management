@extends('layouts.app')

@section('title', 'Profile')

@section('shell', 'shell-savings')

@section('content')
    @include('partials.header')

    @php
        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->take(2)
            ->implode('');
    @endphp

    <section class="card profile-hero">
        <div class="profile-avatar" aria-hidden="true">{{ $initials !== '' ? $initials : 'U' }}</div>
        <div>
            <h1>Profile</h1>
            <p class="lede">{{ $user->name }} · {{ $user->email }}</p>
        </div>
    </section>

    <div class="profile-grid">
        <section class="card">
            <h2><x-icon name="user" /> Account details</h2>
            <p class="hint">Update your name and email used for login.</p>

            @if (session('status'))
                <p class="status">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('account.update') }}">
                @csrf
                @method('PUT')

                <div class="fields">
                    <div class="wide">
                        <label for="name">Name</label>
                        <input id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="wide">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="wide">
                        <button class="button" type="submit">Save profile</button>
                    </div>
                </div>
            </form>
        </section>

        <section class="card">
            <h2><x-icon name="lock" /> Password</h2>
            <p class="hint">Use at least 8 characters for a stronger password.</p>

            @if (session('password_status'))
                <p class="status">{{ session('password_status') }}</p>
            @endif

            <form method="POST" action="{{ route('account.password') }}">
                @csrf
                @method('PUT')

                <div class="fields">
                    <div class="wide">
                        <label for="current_password">Current password</label>
                        <input id="current_password" name="current_password" type="password" required>
                        @error('current_password', 'password') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="wide">
                        <label for="password">New password</label>
                        <input id="password" name="password" type="password" minlength="8" required>
                        @error('password', 'password') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="wide">
                        <label for="password_confirmation">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required>
                    </div>

                    <div class="wide">
                        <button class="button" type="submit">Update password</button>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection
