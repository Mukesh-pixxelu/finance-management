@extends('layouts.app')

@section('title', 'Account')

@section('shell', 'shell-wide')

@section('content')
    @include('partials.header')

    <div class="account-layout">
    <h1><x-icon name="user" class="icon-lg" /> Profile</h1>
    <p class="lede">Update your profile or password.</p>

    <section class="card">
        <h2>Profile</h2>

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
        <h2>Password</h2>

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

                <div>
                    <label for="password">New password</label>
                    <input id="password" name="password" type="password" required>
                    @error('password', 'password') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required>
                </div>

                <div class="wide">
                    <button class="button" type="submit">Update password</button>
                </div>
            </div>
        </form>
    </section>
    </div>
@endsection
