@extends('layouts.app')

@section('title', isset($product->id) ? 'Edit Product | GloryStock' : 'Add Product | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h3 class="fw-800 m-0">{{ isset($product->id) ? 'Edit Product' : 'Add New Product' }}</h3>
                @if (isset($product->id))
                    <span class="badge bg-dark">ID: #{{ $product->id }}</span>
                @endif
            </div>
            <p class="text-muted small mb-4">
                {{ isset($product->id) ? 'Modify product details and stock levels.' : 'Enter product details to update your inventory.' }}
            </p>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small">
                    @foreach ($errors->all() as $error)
                        <div>❌ {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ $route }}" method="POST">
                @csrf
                @if ($method === 'PUT')
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Product Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">SKU / Code</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Selling Price (TZS)</label>
                        <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}" min="1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Stock Quantity</label>
                        <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock ?? 0) }}" min="0" required>
                    </div>
                </div>

                @if (isset($product->id))
                    <p class="text-muted mb-0 mt-2" style="font-size: 0.75rem;">
                        * Changing the stock value will create an entry in the <strong>Stock Audit Trail</strong>.
                    </p>
                @endif

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-light flex-fill fw-bold">Cancel</a>
                    <button type="submit" class="btn btn-dark flex-fill fw-bold text-uppercase">
                        {{ isset($product->id) ? 'Save Changes' : 'Save Product' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
