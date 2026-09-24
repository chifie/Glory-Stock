@extends('layouts.app')

@section('title', 'Categories | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="d-flex align-items-center mb-4 border-bottom pb-3">
    <img src="{{ asset('logo.png') }}" alt="GloryStock" class="page-logo">
    <div>
        <h3 class="fw-800 mb-0">Product Categories</h3>
        <p class="text-muted small mb-0">Organize your inventory into groups</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card p-4">
            <h5 class="fw-bold mb-4">New Category</h5>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small">❌ {{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Category Name</label>
                    <input type="text" name="name" class="form-control form-control-lg" placeholder="e.g. Beverages" value="{{ old('name') }}" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Create Category</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <h5 class="fw-bold mb-4">Existing Categories</h5>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Category Name</th>
                            <th class="text-end pe-4">Total Products</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $category->id }}</td>
                                <td class="fw-bold">{{ $category->name }}</td>
                                <td class="text-end pe-4">
                                    <span class="badge bg-light text-dark border px-3">{{ $category->products_count }} Items</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-5 text-muted">No categories yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
