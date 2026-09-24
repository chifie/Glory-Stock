@extends('layouts.app')

@section('title', 'POS | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="d-flex align-items-center mb-4 border-bottom pb-3">
    <img src="{{ asset('logo.png') }}" alt="GloryStock" class="page-logo">
    <div>
        <h3 class="fw-800 mb-0">POS Terminal</h3>
        <p class="text-muted small mb-0">Process customer sales quickly and securely.</p>
    </div>
</div>

<div class="row justify-content-center g-4">
    <div class="col-md-5">
        @if (request('success'))
            <div class="alert alert-success border-0 shadow-sm text-center py-4 mb-4" style="border-radius: 16px;">
                <div class="display-6 mb-2">✅</div>
                <h4 class="fw-bold">Transaction Complete</h4>
                <p class="text-muted">Total Paid: <span class="fw-bold text-dark">{{ number_format((float) request('amount')) }} TZS</span></p>
                <a href="{{ route('receipt.show', request('receipt')) }}" target="_blank" class="btn btn-dark fw-bold px-4">🖨️ Print Receipt</a>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm">⚠️ {{ $errors->first() }}</div>
        @endif

        <div class="card mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0 text-uppercase" style="letter-spacing: 1px;">Checkout</h5>
                    <span class="badge bg-light text-dark border p-2 small">Cashier: {{ auth()->user()->username }}</span>
                </div>

                <form method="POST" action="{{ route('pos.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Select Item</label>
                        <select name="product_id" class="form-select form-select-lg border-0 bg-light" required>
                            <option value="">-- Choose Product --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} — {{ number_format((float) $product->price) }} /= (Stock: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Quantity</label>
                        <input type="number" name="quantity" class="form-control form-control-lg border-0 bg-light" min="1" value="{{ old('quantity', 1) }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sale w-100 shadow-sm text-uppercase" style="background: #0f172a; border: none;">Process Sale</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold m-0">Recent Sales</h5>
            <a href="{{ route('sales.history') }}" class="small text-decoration-none">View All</a>
        </div>
        <div class="card shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-uppercase text-muted">
                            <th class="ps-3">Time</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th class="text-end pe-3">Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentSales as $sale)
                            <tr>
                                <td class="ps-3 small">{{ $sale->sale_date->format('H:i A') }}</td>
                                <td class="fw-bold">{{ $sale->product_name }}</td>
                                <td><span class="badge bg-light text-dark">x{{ $sale->quantity }}</span></td>
                                <td class="fw-bold">{{ number_format((float) $sale->total_price) }} /=</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('receipt.show', $sale->id) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill">🖨️ View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No sales yet today.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('stock.log') }}" class="btn btn-light btn-sm fw-bold text-muted">🛡️ View Stock Audit Trail</a>
        </div>
    </div>
</div>
@endsection
