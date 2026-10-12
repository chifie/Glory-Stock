@extends('layouts.app')

@section('title', 'Categories | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="flex items-center gap-4 mb-6 border-b border-slate-200 pb-4">
    <img src="{{ asset('logo.png') }}" alt="GloryStock" class="h-11 w-auto object-contain">
    <div>
        <h3 class="text-xl font-extrabold mb-0">Product Categories</h3>
        <p class="text-slate-500 text-sm mb-0">Organize your inventory into groups</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-4">
        <div class="rounded-xl bg-white shadow-md p-5">
            <h5 class="font-bold mb-5">New Category</h5>

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700">&#10060; {{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Category Name</label>
                    <input type="text" name="name" id="name" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-blue-500 focus:outline-none" placeholder="e.g. Beverages" value="{{ old('name') }}" required>
                </div>
                <button type="submit" class="w-full rounded-lg bg-blue-600 py-2.5 font-bold text-white hover:bg-blue-700 transition">Create Category</button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-8">
        <h5 class="font-bold mb-4">Existing Categories</h5>
        <div class="rounded-xl bg-white shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left align-middle">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pl-5 py-3">ID</th>
                            <th class="py-3">Category Name</th>
                            <th class="pr-5 py-3 text-right">Total Products</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-slate-50">
                                <td class="pl-5 py-3 text-slate-500 text-sm">{{ $category->id }}</td>
                                <td class="py-3 font-bold">{{ $category->name }}</td>
                                <td class="pr-5 py-3 text-right">
                                    <span class="rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs">{{ $category->products_count }} Items</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-10 text-slate-500">No categories yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
