@extends('layouts.app')

@section('title', 'Stock Audit Trail | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="mb-6">
    <h4 class="text-lg font-extrabold text-slate-900">Stock Movement History</h4>
    <p class="text-slate-500">Every manual change and sale recorded for accountability.</p>
</div>

<div class="rounded-xl bg-white shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left align-middle">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="pl-5 py-3">Timestamp</th>
                    <th class="py-3">Product Details</th>
                    <th class="py-3">Type</th>
                    <th class="py-3 text-center">Quantity</th>
                    <th class="py-3">Reason / Reference</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($logs as $log)
                    @php($isPositive = $log->change_qty > 0)
                    <tr class="hover:bg-slate-50">
                        <td class="pl-5 py-3 text-sm text-slate-500">
                            {{ $log->created_at->format('M d, Y') }}<br>
                            <span class="font-bold">{{ $log->created_at->format('H:i A') }}</span>
                        </td>
                        <td class="py-3">
                            <div class="font-bold">{{ $log->product_name }}</div>
                            <div class="text-xs text-slate-500">{{ $log->sku }}</div>
                        </td>
                        <td class="py-3">
                            @if ($isPositive)
                                <span class="rounded-md bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-800">INCOMING</span>
                            @else
                                <span class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-bold text-red-800">OUTGOING</span>
                            @endif
                        </td>
                        <td class="py-3 text-center text-lg font-extrabold {{ $isPositive ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ ($isPositive ? '+' : '').$log->change_qty }}
                        </td>
                        <td class="py-3">
                            <span class="text-sm font-bold uppercase text-slate-900">{{ $log->reason }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-10 text-slate-500">No stock movements found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
