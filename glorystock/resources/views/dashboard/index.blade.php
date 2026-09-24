@extends('layouts.app')

@section('title', 'Master Dashboard | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card bg-white border-start border-primary border-5 shadow-sm">
            <h6 class="text-muted small fw-bold text-uppercase">Stock Valuation</h6>
            <h3 class="fw-800 mb-0">{{ number_format($totalInventoryValue) }} <small class="fs-6 fw-normal">TZS</small></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-white border-start border-success border-5 shadow-sm">
            <h6 class="text-muted small fw-bold text-uppercase">Today's Revenue</h6>
            <h3 class="fw-800 mb-0 text-success">{{ number_format($todayRevenue) }} <small class="fs-6 fw-normal">TZS</small></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-white border-start border-danger border-5 shadow-sm">
            <h6 class="text-muted small fw-bold text-uppercase">Monthly Expenses</h6>
            <h3 class="fw-800 mb-0 text-danger">{{ number_format($monthlyExpenses) }} <small class="fs-6 fw-normal text-muted">TZS</small></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-white border-start border-warning border-5 shadow-sm">
            <h6 class="text-muted small fw-bold text-uppercase">Net Profit (MTD)</h6>
            <h3 class="fw-800 mb-0 {{ $netProfit >= 0 ? 'text-dark' : 'text-danger' }}">
                {{ number_format($netProfit) }} <small class="fs-6 fw-normal text-muted">TZS</small>
            </h3>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body p-4">
        <form action="{{ route('dashboard') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="small fw-bold text-muted">Search Products</label>
                <input type="text" name="search" class="form-control" placeholder="SKU or Name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted">Category</label>
                <select name="category_id" class="form-select">
                    <option value="0">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="low_stock" value="1" id="lowStock" {{ request('low_stock') ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-danger" for="lowStock">Low Stock Only</label>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-dark w-100 fw-bold">Apply Filters</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Inventory List</h5>
        <div>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-danger btn-sm fw-bold me-2">💸 Expenses</a>
                <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm fw-bold">➕ Add New Product</a>
            @endif
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">SKU</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td class="ps-4 text-muted small fw-bold">{{ $product->sku }}</td>
                        <td class="fw-bold">{{ $product->name }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $product->category->name ?? 'Uncategorized' }}</span></td>
                        <td class="fw-bold">{{ number_format((float) $product->price) }} /=</td>
                        <td class="fw-bold {{ $product->stock <= 5 ? 'text-danger' : '' }}">{{ $product->stock }}</td>
                        <td>
                            @if ($product->stock <= config('glorystock.low_stock_threshold'))
                                <span class="badge badge-low border border-danger">REORDER</span>
                            @else
                                <span class="badge bg-success">HEALTHY</span>
                            @endif
                        </td>
                        <td>
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            @else
                                <span class="badge bg-light text-muted">Read-Only</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No products matching your search criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
