@extends('layouts.app')

@section('title', 'Master Dashboard | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="stat-card rounded-2xl bg-white border-l-4 border-blue-500 p-5 shadow-sm">
        <h6 class="text-xs font-bold text-slate-500 uppercase tracking-wide">Stock Valuation</h6>
        <h3 class="text-2xl font-extrabold mb-0">{{ number_format($totalInventoryValue) }} <small class="text-base font-normal text-slate-400">TZS</small></h3>
    </div>
    <div class="stat-card rounded-2xl bg-white border-l-4 border-emerald-500 p-5 shadow-sm">
        <h6 class="text-xs font-bold text-slate-500 uppercase tracking-wide">Today's Revenue</h6>
        <h3 class="text-2xl font-extrabold mb-0 text-emerald-600">{{ number_format($todayRevenue) }} <small class="text-base font-normal text-slate-400">TZS</small></h3>
    </div>
    <div class="stat-card rounded-2xl bg-white border-l-4 border-red-500 p-5 shadow-sm">
        <h6 class="text-xs font-bold text-slate-500 uppercase tracking-wide">Monthly Expenses</h6>
        <h3 class="text-2xl font-extrabold mb-0 text-red-600">{{ number_format($monthlyExpenses) }} <small class="text-base font-normal text-slate-400">TZS</small></h3>
    </div>
    <div class="stat-card rounded-2xl bg-white border-l-4 border-amber-500 p-5 shadow-sm">
        <h6 class="text-xs font-bold text-slate-500 uppercase tracking-wide">Net Profit (MTD)</h6>
        <h3 class="text-2xl font-extrabold mb-0 {{ $netProfit >= 0 ? 'text-slate-900' : 'text-red-600' }}">
            {{ number_format($netProfit) }} <small class="text-base font-normal text-slate-400">TZS</small>
        </h3>
    </div>
</div>

<div class="rounded-xl bg-white shadow-md mb-6 p-5">
    <form action="{{ route('dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label for="search" class="block text-xs font-bold text-slate-500 mb-1">Search Products</label>
            <input type="text" name="search" id="search" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none" placeholder="SKU or Name..." value="{{ request('search') }}">
        </div>
        <div>
            <label for="category_id" class="block text-xs font-bold text-slate-500 mb-1">Category</label>
            <select name="category_id" id="category_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                <option value="0">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="flex items-center gap-2 font-bold text-red-600 text-sm mb-2">
                <input type="checkbox" name="low_stock" value="1" class="h-4 w-4 rounded accent-red-600" {{ request('low_stock') ? 'checked' : '' }}>
                Low Stock Only
            </label>
        </div>
        <div>
            <button type="submit" class="w-full rounded-lg bg-slate-900 py-2 font-bold text-white hover:bg-slate-800 transition">Apply Filters</button>
        </div>
    </form>
</div>

<div class="rounded-xl bg-white shadow-md overflow-hidden">
    <div class="flex justify-between items-center border-b border-slate-200 bg-white px-5 py-3">
        <h5 class="font-bold mb-0">Inventory List</h5>
        <div>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('expenses.index') }}" class="inline-block mr-2 rounded-lg border border-red-300 px-3 py-1.5 text-sm font-bold text-red-600 hover:bg-red-50 transition">&#128176; Expenses</a>
                <a href="{{ route('products.create') }}" class="inline-block rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-bold text-white hover:bg-blue-700 transition">&#10133; Add New Product</a>
            @endif
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left align-middle">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="pl-5 py-3">SKU</th>
                    <th class="py-3">Product Name</th>
                    <th class="py-3">Category</th>
                    <th class="py-3">Price</th>
                    <th class="py-3">Stock</th>
                    <th class="py-3">Status</th>
                    <th class="pr-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($products as $product)
                    <tr class="hover:bg-slate-50">
                        <td class="pl-5 py-3 text-slate-500 text-sm font-bold">{{ $product->sku }}</td>
                        <td class="py-3 font-bold">{{ $product->name }}</td>
                        <td class="py-3"><span class="rounded-full border border-slate-200 bg-slate-100 px-2.5 py-0.5 text-xs">{{ $product->category->name ?? 'Uncategorized' }}</span></td>
                        <td class="py-3 font-bold">{{ number_format((float) $product->price) }} /=</td>
                        <td class="py-3 font-bold {{ $product->stock <= 5 ? 'text-red-600' : '' }}">{{ $product->stock }}</td>
                        <td class="py-3">
                            @if ($product->stock <= config('glorystock.low_stock_threshold'))
                                <span class="rounded-full border border-red-300 bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-800">REORDER</span>
                            @else
                                <span class="rounded-full bg-emerald-500 px-2.5 py-0.5 text-xs font-bold text-white">HEALTHY</span>
                            @endif
                        </td>
                        <td class="pr-5 py-3 text-right">
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('products.edit', $product) }}" class="inline-block rounded-lg border border-blue-300 px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 transition">Edit</a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline ml-2"
                                      onsubmit="return confirm('Delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-300 px-3 py-1 text-sm text-red-600 hover:bg-red-50 transition">Delete</button>
                                </form>
                            @else
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs text-slate-400">Read-Only</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-slate-500">No products matching your search criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
