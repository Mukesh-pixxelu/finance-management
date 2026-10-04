@extends('layouts.app')

@section('title', 'Edit pension')

@section('shell', 'shell-savings')

@section('content')
    @include('partials.header')

    <h1><x-icon name="pencil" class="icon-lg" /> Edit pension</h1>
    <p class="lede">Update this pension account.</p>

    <section class="card">
        <form method="POST" action="{{ route('pensions.update', $pension) }}">
            @csrf
            @method('PUT')
            @include('pensions.partials.form-fields', ['pension' => $pension])
            <div class="form-actions">
                <button class="button" type="submit"><x-icon name="plus" /> Save changes</button>
                <a class="button-quiet" href="{{ route('pensions.index') }}">Cancel</a>
            </div>
        </form>
    </section>
@endsection
