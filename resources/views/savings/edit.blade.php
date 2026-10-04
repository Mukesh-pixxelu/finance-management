@extends('layouts.app')

@section('title', 'Edit saving')

@section('shell', 'shell-savings')

@section('content')
    @include('partials.header')

    <h1><x-icon name="pencil" class="icon-lg" /> Edit saving</h1>
    <p class="lede">Update this savings account, FD, or RD.</p>

    <section class="card">
        <form method="POST" action="{{ route('savings.update', $saving) }}">
            @csrf
            @method('PUT')
            @include('savings.partials.form-fields', ['saving' => $saving, 'extended' => true])
            <div class="form-actions">
                <button class="button" type="submit"><x-icon name="plus" /> Save changes</button>
                <a class="button-quiet" href="{{ route('savings.index') }}">Cancel</a>
            </div>
        </form>
    </section>
@endsection
