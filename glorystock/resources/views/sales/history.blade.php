@extends('layouts.app')

@section('title', 'Sales History | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-extrabold mb-0">Sales Analytics</h2>
    <button onclick="window.print()" class="no-print rounded-lg border border-slate-400 bg-white px-4 py-2 font-semibold text-slate-700 hover:bg-slate-100 transition">&#128424;&#65039; Export PDF/Print</button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
    <div class="rounded-xl bg-blue-600 p-5 text-white shadow-md">
        <h6 class="text-xs font-bold uppercase tracking-wide opacity-75">Total Period Revenue</h6>
        <h2 class="text-3xl font-extrabold mb-0">{{ number_format($totalRevenue) }} <small class="text-base font-normal">TZS</small></h2>
    </div>
    <div class="rounded-xl bg-emerald-700 p-5 text-white shadow-md">
        <h6 class="text-xs font-bold uppercase tracking-wide opacity-75">Top Moving Product</h6>
        <h2 class="text-3xl font-extrabold mb-0">{{ $topProduct->name ?? 'N/A' }}</h2>
        <small class="opacity-90">Units Sold: {{ $topProduct->total_qty ?? 0 }}</small>
    </div>
</div>

<div class="rounded-xl bg-white shadow-md mb-6 p-5">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div>
            <label for="start_date" class="block text-xs font-bold text-slate-500 mb-1">From Date</label>
            <input type="date" name="start_date" id="start_date" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none" value="{{ $startDate->format('Y-m-d') }}">
        </div>
        <div>
            <label for="end_date" class="block text-xs font-bold text-slate-500 mb-1">To Date</label>
            <input type="date" name="end_date" id="end_date" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none" value="{{ $endDate->format('Y-m-d') }}">
        </div>
        <div>
            <button type="submit" class="w-full rounded-lg bg-slate-900 py-2 font-bold text-white hover:bg-slate-800 transition">Update Report</button>
        </div>
    </form>
</div>

<div class="rounded-xl bg-white shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left align-middle">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="pl-5 py-3">Timestamp</th>
                    <th class="py-3">Product Details</th>
                    <th class="py-3">Quantity</th>
                    <th class="pr-5 py-3 text-right">Total Price</th>
                    @if (auth()->user()->isAdmin())
                        <th class="pr-5 py-3 text-right">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($sales as $sale)
                    <tr class="hover:bg-slate-50">
                        <td class="pl-5 py-3 text-sm text-slate-500">{{ $sale->sale_date->format('d M Y, H:i') }}</td>
                        <td class="py-3">
                            <div class="font-bold">{{ $sale->product_name }}</div>
                            <div class="text-xs text-slate-500">SKU: {{ $sale->sku }}</div>
                        </td>
                        <td class="py-3"><span class="rounded-full border border-slate-200 bg-slate-100 px-3 py-0.5 text-xs">{{ $sale->quantity }}</span></td>
                        <td class="pr-5 py-3 text-right font-extrabold">{{ number_format((float) $sale->total_price) }} /=</td>
                        @if (auth()->user()->isAdmin())
                            <td class="pr-5 py-3 text-right">
                                <form action="{{ route('sales.void', $sale) }}" method="POST"
                                      onsubmit="return confirm('Void this sale and restore stock?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100 transition">Void</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 5 : 4 }}" class="text-center py-10 text-slate-500">No sales recorded for this period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
