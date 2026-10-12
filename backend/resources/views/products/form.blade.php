@extends('layouts.app')

@section('title', isset($product->id) ? 'Edit Product | GloryStock' : 'Add Product | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="flex justify-center">
    <div class="card w-full max-w-2xl rounded-xl bg-white shadow-md p-6">
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-xl font-extrabold mb-0">{{ isset($product->id) ? 'Edit Product' : 'Add New Product' }}</h3>
            @if (isset($product->id))
                <span class="rounded-full bg-slate-900 px-3 py-1 text-xs font-bold text-white">ID: #{{ $product->id }}</span>
            @endif
        </div>
        <p class="text-slate-500 text-sm mb-5">
            {{ isset($product->id) ? 'Modify product details and stock levels.' : 'Enter product details to update your inventory.' }}
        </p>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <div>&#10060; {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ $route }}" method="POST">
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif

            <div class="mb-4">
                <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Product Name</label>
                <input type="text" name="name" id="name" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none" value="{{ old('name', $product->name) }}" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="sku" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">SKU / Code</label>
                    <input type="text" name="sku" id="sku" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none" value="{{ old('sku', $product->sku) }}" required>
                </div>
                <div>
                    <label for="category_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Category</label>
                    <select name="category_id" id="category_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none" required>
                        <option value="">-- Select Category --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="price" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Selling Price (TZS)</label>
                    <input type="number" name="price" id="price" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none" value="{{ old('price', $product->price) }}" min="1" required>
                </div>
                <div>
                    <label for="stock" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Stock Quantity</label>
                    <input type="number" name="stock" id="stock" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none" value="{{ old('stock', $product->stock ?? 0) }}" min="0" required>
                </div>
            </div>

            @if (isset($product->id))
                <p class="text-slate-500 mb-0 mt-3 text-xs">
                    * Changing the stock value will create an entry in the <strong>Stock Audit Trail</strong>.
                </p>
            @endif

            <div class="flex gap-3 mt-6">
                <a href="{{ route('dashboard') }}" class="flex-1 text-center rounded-lg bg-slate-100 py-2.5 font-bold text-slate-700 hover:bg-slate-200 transition">Cancel</a>
                <button type="submit" class="flex-1 rounded-lg bg-slate-900 py-2.5 font-bold uppercase text-white hover:bg-slate-800 transition">
                    {{ isset($product->id) ? 'Save Changes' : 'Save Product' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
