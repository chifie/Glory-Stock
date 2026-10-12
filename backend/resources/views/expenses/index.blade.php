@extends('layouts.app')

@section('title', 'Expense Tracker | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="flex items-center gap-4 mb-6 border-b border-slate-200 pb-4">
    <img src="{{ asset('logo.png') }}" alt="GloryStock" class="h-11 w-auto object-contain">
    <div>
        <h3 class="text-xl font-extrabold mb-0">Expense Management</h3>
        <p class="text-slate-500 text-sm mb-0">Track and record business outgoings</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-4">
        <div class="rounded-xl bg-white shadow-md p-5">
            <h5 class="font-bold mb-5">Record New Expense</h5>

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700">&#10060; {{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('expenses.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="category" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Category</label>
                    <select name="category" id="category" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none" required>
                        @foreach (\App\Models\Expense::CATEGORIES as $categoryOption)
                            <option value="{{ $categoryOption }}" {{ old('category') === $categoryOption ? 'selected' : '' }}>{{ $categoryOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="amount" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Amount (TZS)</label>
                    <div class="flex items-center rounded-lg border border-slate-300 focus-within:border-blue-500">
                        <input type="number" name="amount" id="amount" class="w-full rounded-lg px-3 py-2 focus:outline-none" placeholder="e.g. 50000" step="0.01" min="0.01" value="{{ old('amount') }}" required>
                        <span class="px-3 text-sm font-bold text-slate-400">/=</span>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="expense_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Date</label>
                    <input type="date" name="expense_date" id="expense_date" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none" value="{{ old('expense_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="mb-5">
                    <label for="description" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Description</label>
                    <textarea name="description" id="description" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none" placeholder="What was this for?">{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="w-full rounded-lg bg-slate-900 py-3 shadow-sm font-bold text-white hover:bg-slate-800 transition">
                    &#128190; Save to Records
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-8">
        <div class="rounded-xl bg-white shadow-md overflow-hidden h-full">
            <div class="flex justify-between items-center border-b border-slate-200 bg-white px-5 py-3">
                <h5 class="mb-0 font-bold">Recent Outgoings</h5>
                <span class="rounded-full border border-slate-200 bg-slate-100 px-3 py-1.5 text-xs font-bold">{{ $expenses->count() }} Total Items</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left align-middle">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pl-5 py-3">Date</th>
                            <th class="py-3">Category</th>
                            <th class="py-3">Details</th>
                            <th class="pr-5 py-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($expenses as $expense)
                            <tr class="hover:bg-slate-50">
                                <td class="pl-5 py-3 text-sm text-slate-500">&#128197; {{ $expense->expense_date->format('M d, Y') }}</td>
                                <td class="py-3">
                                    <span class="rounded-full border border-red-200 bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-700">
                                        {{ $expense->category }}
                                    </span>
                                </td>
                                <td class="py-3 text-sm text-slate-800 font-medium">{{ $expense->description }}</td>
                                <td class="pr-5 py-3 text-right font-bold text-slate-900">{{ number_format((float) $expense->amount) }} /=</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-10 text-slate-500">No expenses recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
