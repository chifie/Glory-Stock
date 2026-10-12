<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GloryStock_Receipt_{{ $sale->id }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <style>
        @media print {
            body { background: white; }
            .no-print { display: none !important; }
            .receipt-card { box-shadow: none; margin: 0 auto; width: 100%; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900">

<div class="no-print text-center mt-8">
    <button onclick="window.print()" class="rounded-full bg-slate-900 px-8 py-2 font-bold text-white shadow hover:bg-slate-800 transition">
        &#128424;&#65039; PRINT RECEIPT
    </button>
    <div class="mt-3">
        <a href="{{ route('pos.index') }}" class="text-slate-500 text-sm hover:underline">
            &larr; Back to POS Dashboard
        </a>
    </div>
</div>

<div class="receipt-card relative w-[90mm] max-w-full mx-auto my-10 rounded-2xl overflow-hidden bg-white shadow-xl">
    <div class="bg-gradient-to-br from-slate-900 to-slate-700 px-5 py-7 text-center text-white">
        <img src="{{ asset('logo.png') }}" alt="GloryStock" class="mx-auto mb-3 h-14 w-auto rounded-xl bg-white p-2">
        <h2 class="m-0 text-2xl font-extrabold tracking-widest">GLORYSTOCK</h2>
        <div class="text-xs opacity-75">Inventory Management System</div>
    </div>

    <div class="p-6">
        <div class="mb-5 grid grid-cols-2 gap-2.5 text-xs text-slate-500">
            <div>
                <span class="block font-semibold uppercase text-slate-400">Receipt Number</span>
                <span class="font-bold text-slate-800">#GS-{{ str_pad((string) $sale->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="text-right">
                <span class="block font-semibold uppercase text-slate-400">Date &amp; Time</span>
                <span class="font-bold text-slate-800">{{ $sale->sale_date->format('M d, Y | H:i') }}</span>
            </div>
            <div>
                <span class="block font-semibold uppercase text-slate-400">Served By</span>
                <span class="font-bold text-slate-800">{{ strtoupper($sale->cashier_name ?? 'Staff') }}</span>
            </div>
            <div class="text-right">
                <span class="block font-semibold uppercase text-slate-400">Contact Store</span>
                <span class="font-bold text-slate-800">{{ config('glorystock.store_phone') }}</span>
            </div>
        </div>

        <table class="w-full border-collapse mb-5">
            <thead>
                <tr>
                    <th class="border-b-2 border-slate-100 py-2.5 text-left text-[0.7rem] uppercase text-slate-400">Description</th>
                    <th class="border-b-2 border-slate-100 py-2.5 text-center text-[0.7rem] uppercase text-slate-400">Qty</th>
                    <th class="border-b-2 border-slate-100 py-2.5 text-right text-[0.7rem] uppercase text-slate-400">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-3 text-sm font-bold text-slate-800">{{ $sale->product->name }}</td>
                    <td class="py-3 text-center text-sm text-slate-500">x{{ $sale->quantity }}</td>
                    <td class="py-3 text-right text-sm font-bold">{{ number_format((float) $sale->total_price) }} /=</td>
                </tr>
            </tbody>
        </table>

        <div class="rounded-xl bg-slate-50 p-4 mt-2.5">
            <div class="mb-1 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">SUBTOTAL</span>
                <span class="text-xs font-bold">{{ number_format((float) $sale->total_price) }} /=</span>
            </div>
            <div class="mb-2 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">TAX (VAT 0%)</span>
                <span class="text-xs font-bold">0.00</span>
            </div>
            <div class="flex items-center justify-between border-t border-slate-200 pt-2">
                <span class="font-extrabold text-slate-900">GRAND TOTAL</span>
                <span class="font-mono text-xl font-extrabold text-slate-900">{{ number_format((float) $sale->total_price) }} /=</span>
            </div>
        </div>
    </div>

    <div class="border-t border-dashed border-slate-200 px-5 py-5 text-center text-xs text-slate-400">
        <div class="mb-1 font-bold text-slate-800">Thank you for your business!</div>
        <div>Please visit us again for more quality stock.</div>
        <div class="mt-3 text-[0.6rem] font-bold uppercase tracking-widest">
            {{ config('glorystock.store_phone') }} &#8226; {{ config('glorystock.store_city') }} &#8226; GloryStock
        </div>
    </div>
</div>

</body>
</html>
