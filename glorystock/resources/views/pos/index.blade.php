@extends('layouts.app')

@section('title', 'POS | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="flex items-center gap-4 mb-6 border-b border-slate-200 pb-4">
    <img src="{{ asset('logo.png') }}" alt="GloryStock" class="h-11 w-auto object-contain">
    <div>
        <h3 class="text-xl font-extrabold mb-0">POS Terminal</h3>
        <p class="text-slate-500 text-sm mb-0">Process customer sales quickly and securely.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-5">
        @if (request('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 text-center py-6 shadow-sm">
                <div class="text-4xl mb-2">&#9989;</div>
                <h4 class="font-bold text-lg">Transaction Complete</h4>
                <p class="text-slate-600">Total Paid: <span class="font-bold text-slate-900">{{ number_format((float) request('amount')) }} TZS</span></p>
                <a href="{{ route('receipt.show', request('receipt')) }}" target="_blank" class="inline-block mt-2 rounded-lg bg-slate-900 px-5 py-2 font-bold text-white hover:bg-slate-800 transition">&#128424;&#65039; Print Receipt</a>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm">
                &#9888;&#65039; {{ $errors->first() }}
            </div>
        @endif

        <div class="rounded-xl bg-white shadow-md mb-6 overflow-hidden">
            <div class="p-5">
                <div class="flex justify-between items-center mb-5">
                    <h5 class="font-bold text-sm uppercase tracking-widest">Checkout</h5>
                    <span class="rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs font-medium">Cashier: {{ auth()->user()->username }}</span>
                </div>

                <form method="POST" action="{{ route('pos.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="product_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Select Item</label>
                        <select name="product_id" id="product_id" class="w-full rounded-lg border-0 bg-slate-100 px-4 py-3 text-base focus:ring-2 focus:ring-blue-300 focus:outline-none" required>
                            <option value="">-- Choose Product --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} &mdash; {{ number_format((float) $product->price) }} /= (Stock: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="quantity" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Quantity</label>
                        <input type="number" name="quantity" id="quantity" class="w-full rounded-lg border-0 bg-slate-100 px-4 py-3 text-base focus:ring-2 focus:ring-blue-300 focus:outline-none" min="1" value="{{ old('quantity', 1) }}" required>
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-slate-900 shadow-sm py-3 font-bold uppercase tracking-wide text-white hover:bg-slate-800 transition">Process Sale</button>
                </form>
            </div>
        </div>
    </div>

    <div class="lg:col-span-7">
        <div class="flex justify-between items-center mb-3">
            <h5 class="font-bold text-lg">Recent Sales</h5>
            <a href="{{ route('sales.history') }}" class="text-sm text-blue-600 hover:underline">View All</a>
        </div>
        <div class="rounded-xl bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left align-middle">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pl-5 py-3">Time</th>
                            <th class="py-3">Item</th>
                            <th class="py-3">Qty</th>
                            <th class="py-3">Total</th>
                            <th class="pr-5 py-3 text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentSales as $sale)
                            <tr class="hover:bg-slate-50">
                                <td class="pl-5 py-3 text-sm">{{ $sale->sale_date->format('H:i A') }}</td>
                                <td class="py-3 font-bold">{{ $sale->product_name }}</td>
                                <td class="py-3"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs">x{{ $sale->quantity }}</span></td>
                                <td class="py-3 font-bold">{{ number_format((float) $sale->total_price) }} /=</td>
                                <td class="pr-5 py-3 text-right">
                                    <a href="{{ route('receipt.show', $sale->id) }}" target="_blank" class="inline-block rounded-full border border-slate-300 px-3 py-1 text-xs hover:bg-slate-900 hover:text-white transition">&#128424;&#65039; View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-5 text-slate-500">No sales yet today.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            <a href="{{ route('stock.log') }}" class="inline-block rounded-lg bg-white px-4 py-2 text-sm font-bold text-slate-500 shadow-sm hover:bg-slate-50 transition">&#128737;&#65039; View Stock Audit Trail</a>
        </div>
    </div>
</div>
@endsection
