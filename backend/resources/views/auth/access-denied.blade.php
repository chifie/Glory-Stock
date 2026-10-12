@extends('layouts.app')

@section('title', 'Access Denied | GloryStock')

@section('content')
<div class="flex justify-center py-10">
    <div class="card w-full max-w-lg bg-white rounded-2xl p-8 text-center shadow-md">
        <h1 class="text-6xl mb-3">&#128683;</h1>
        <h3 class="text-xl font-bold mb-2">Access Denied</h3>
        <p class="text-slate-500 mb-6">Hello {{ auth()->user()->username }}, you don't have Admin rights.</p>
        <a href="{{ route('pos.index') }}" class="inline-block rounded-lg bg-slate-900 px-5 py-2 font-semibold text-white hover:bg-slate-800 transition">Back to POS</a>
    </div>
</div>
@endsection
