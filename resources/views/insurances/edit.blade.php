@extends('layouts.app')

@section('title', 'Edit policy')

@section('shell', 'shell-savings')

@section('content')
    @include('partials.header')

    <h1><x-icon name="pencil" class="icon-lg" /> Edit policy</h1>
    <p class="lede">Update this insurance policy.</p>

    <section class="card">
        <form method="POST" action="{{ route('insurances.update', $insurance) }}">
            @csrf
            @method('PUT')
            @include('insurances.partials.form-fields', ['insurance' => $insurance])
            <div class="form-actions">
                <button class="button" type="submit"><x-icon name="plus" /> Save changes</button>
                <a class="button-quiet" href="{{ route('insurances.index') }}">Cancel</a>
            </div>
        </form>
    </section>
@endsection
