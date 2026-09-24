@extends('layouts.app')

@section('title', 'Access Denied | GloryStock')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-5 border-0 shadow-sm text-center" style="border-radius: 20px;">
            <h1 class="display-1">🚫</h1>
            <h3 class="fw-bold">Access Denied</h3>
            <p class="text-muted">Hello {{ auth()->user()->username }}, you don't have Admin rights.</p>
            <a href="{{ route('pos.index') }}" class="btn btn-dark px-4 py-2">Back to POS</a>
        </div>
    </div>
</div>
@endsection
