@extends('layouts.app')

@section('title', 'Stock Audit Trail | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-800 text-dark">Stock Movement History</h4>
        <p class="text-muted">Every manual change and sale recorded for accountability.</p>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Timestamp</th>
                    <th>Product Details</th>
                    <th>Type</th>
                    <th class="text-center">Quantity</th>
                    <th>Reason / Reference</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    @php($isPositive = $log->change_qty > 0)
                    <tr>
                        <td class="ps-4 small text-muted">
                            {{ $log->created_at->format('M d, Y') }}<br>
                            <span class="fw-bold">{{ $log->created_at->format('H:i A') }}</span>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $log->product_name }}</div>
                            <div class="text-muted small">{{ $log->sku }}</div>
                        </td>
                        <td>
                            @if ($isPositive)
                                <span class="badge px-3 py-2" style="background: #dcfce7; color: #166534;">INCOMING</span>
                            @else
                                <span class="badge px-3 py-2" style="background: #fee2e2; color: #991b1b;">OUTGOING</span>
                            @endif
                        </td>
                        <td class="text-center fw-800 fs-5 {{ $isPositive ? 'text-success' : 'text-danger' }}">
                            {{ ($isPositive ? '+' : '').$log->change_qty }}
                        </td>
                        <td>
                            <span class="text-dark small fw-bold text-uppercase">{{ $log->reason }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">No stock movements found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
