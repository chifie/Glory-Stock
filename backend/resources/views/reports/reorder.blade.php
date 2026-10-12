<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reorder Report | GloryStock</title>
    <style>
        @media print { .no-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body class="bg-white p-10 leading-normal text-slate-800">

<div class="no-print sticky top-0 z-10 -mx-10 -mt-10 mb-10 flex items-center justify-between border-b border-slate-200 bg-slate-50 px-10 py-4">
    <span class="text-sm text-slate-500">&#128161; Print this list to take to the wholesaler.</span>
    <button onclick="window.print()" class="rounded-md bg-blue-600 px-5 py-2.5 font-bold text-white hover:bg-blue-700 transition">Print Now</button>
</div>

<div class="mb-8 flex items-center justify-between border-b-2 border-slate-100 pb-5">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-900 text-xl font-black text-white">GS</div>
        <div>
            <h1 class="m-0 text-2xl font-extrabold tracking-tight text-slate-900">GLORYSTOCK</h1>
            <p class="m-0 text-[0.8rem] text-slate-500">Inventory Intelligence Report | {{ config('glorystock.store_city') }}</p>
        </div>
    </div>
    <div class="text-right">
        <p class="m-0 font-bold text-slate-800">REORDER SHEET</p>
        <p class="m-0 text-xs text-slate-500">{{ now()->format('D, d M Y') }}</p>
    </div>
</div>

<div class="mb-6 flex items-center justify-between">
    <div class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-bold text-red-800">&#9888;&#65039; {{ $items->count() }} Items Need Restocking</div>
    <div class="text-xs text-slate-500">Prepared by: {{ auth()->user()->username }}</div>
</div>

<table class="mb-16 w-full border-collapse">
    <thead>
        <tr>
            <th class="border-b-2 border-slate-200 p-3 text-left text-[0.7rem] uppercase tracking-wider text-slate-500">SKU</th>
            <th class="border-b-2 border-slate-200 p-3 text-left text-[0.7rem] uppercase tracking-wider text-slate-500">Product Description</th>
            <th class="border-b-2 border-slate-200 p-3 text-left text-[0.7rem] uppercase tracking-wider text-slate-500">Category</th>
            <th class="border-b-2 border-slate-200 p-3 text-left text-[0.7rem] uppercase tracking-wider text-slate-500">Current Stock</th>
            <th class="border-b-2 border-slate-200 p-3 text-left text-[0.7rem] uppercase tracking-wider text-slate-500">Order Qty</th>
            <th class="border-b-2 border-slate-200 p-3 text-left text-[0.7rem] uppercase tracking-wider text-slate-500">Estimated Unit Cost</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($items as $item)
            <tr class="border-b border-slate-100 text-sm hover:bg-slate-50">
                <td class="p-3 font-mono text-slate-500">{{ $item->sku }}</td>
                <td class="p-3"><strong>{{ $item->name }}</strong></td>
                <td class="p-3">{{ $item->category->name ?? 'General' }}</td>
                <td class="p-3"><span class="font-bold text-rose-600">{{ $item->stock }} units left</span></td>
                <td class="p-3"><span class="inline-block w-20 border-b border-slate-300">&nbsp;</span></td>
                <td class="p-3 text-slate-400">{{ number_format((float) $item->price) }} /=</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="p-12 text-center text-slate-500">
                    &#9989; All stock levels are healthy. Nothing to reorder!
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-20 flex justify-between">
    <div class="w-56 border-t border-slate-400 pt-2 text-center text-xs text-slate-500">Store Manager Signature</div>
    <div class="w-56 border-t border-slate-400 pt-2 text-center text-xs text-slate-500">Authorized Approval</div>
</div>

</body>
</html>
