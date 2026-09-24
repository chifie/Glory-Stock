@extends('layouts.app')

@section('title', 'Sales History | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-800 m-0">Sales Analytics</h2>
    <button onclick="window.print()" class="btn btn-outline-dark no-print">🖨️ Export PDF/Print</button>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card p-4 text-white" style="background: #0d6efd;">
            <h6 class="text-uppercase small fw-bold opacity-75">Total Period Revenue</h6>
            <h2 class="fw-800 mb-0">{{ number_format($totalRevenue) }} <small class="fs-6">TZS</small></h2>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-4 text-white" style="background: #198754;">
            <h6 class="text-uppercase small fw-bold opacity-75">Top Moving Product</h6>
            <h2 class="fw-800 mb-0">{{ $topProduct->name ?? 'N/A' }}</h2>
            <small>Units Sold: {{ $topProduct->total_qty ?? 0 }}</small>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold">From Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">To Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-dark w-100 fw-bold">Update Report</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Timestamp</th>
                    <th>Product Details</th>
                    <th>Quantity</th>
                    <th class="text-end pe-4">Total Price</th>
                    @if (auth()->user()->isAdmin())
                        <th class="text-end pe-4">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td class="ps-4 small text-muted">{{ $sale->sale_date->format('d M Y, H:i') }}</td>
                        <td>
                            <div class="fw-bold">{{ $sale->product_name }}</div>
                            <div class="small text-muted">SKU: {{ $sale->sku }}</div>
                        </td>
                        <td><span class="badge bg-light text-dark border px-3">{{ $sale->quantity }}</span></td>
                        <td class="text-end pe-4 fw-800">{{ number_format((float) $sale->total_price) }} /=</td>
                        @if (auth()->user()->isAdmin())
                            <td class="text-end pe-4">
                                <form action="{{ route('sales.void', $sale) }}" method="POST"
                                      onsubmit="return confirm('Void this sale and restore stock?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">Void</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 5 : 4 }}" class="text-center py-5 text-muted">No sales recorded for this period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
