@extends('layouts.app')

@section('title', 'Business Reports | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <div class="d-flex align-items-center">
        <img src="{{ asset('logo.png') }}" alt="GloryStock" class="page-logo">
        <div>
            <h3 class="fw-800 mb-0">Business Analytics</h3>
            <p class="text-muted small mb-0">Financial overview for the last 6 months</p>
        </div>
    </div>
    <button onclick="window.print()" class="btn btn-dark fw-bold rounded-pill px-4">
        <i class="fas fa-print me-2"></i> Print Report
    </button>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0">Revenue vs Expenses</h5>
                <div class="small text-muted">Real-time Performance Graph</div>
            </div>
            <div style="height: 350px;">
                <canvas id="businessChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card p-4 h-100">
            <h6 class="fw-bold mb-3 text-uppercase small text-muted">Monthly Statement</h6>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="small text-muted">
                            <th>Month</th>
                            <th>Revenue</th>
                            <th>Expenses</th>
                            <th>Net Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($months as $index => $month)
                            @php($revenue = $revenueValues[$index])
                            @php($expense = $expenseValues[$index])
                            @php($profit = $revenue - $expense)
                            <tr>
                                <td class="fw-bold text-dark">{{ $month }}</td>
                                <td class="text-success fw-semibold">{{ number_format($revenue) }} /=</td>
                                <td class="text-danger">{{ number_format($expense) }} /=</td>
                                <td class="fw-bold {{ $profit >= 0 ? 'text-primary' : 'text-danger' }}">
                                    {{ ($profit < 0 ? '-' : '+').number_format(abs($profit)) }} /=
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card p-4 h-100">
            <h6 class="fw-bold mb-3 text-uppercase small text-muted">Best Selling Products</h6>
            @forelse ($topProducts as $item)
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <span class="fw-bold text-dark">{{ $item->name }}</span>
                    <span class="badge bg-primary rounded-pill">{{ $item->total_sold }} Units Sold</span>
                </div>
            @empty
                <p class="text-muted small">No sales data yet.</p>
            @endforelse

            <div class="mt-4 pt-2">
                <div class="alert alert-light border small text-center">
                    <i class="fas fa-info-circle me-2"></i> These items generate the highest footfall for GloryStock.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('businessChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($months),
        datasets: [{
            label: 'Total Revenue',
            data: @json($revenueValues),
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#3b82f6'
        }, {
            label: 'Total Expenses',
            data: @json($expenseValues),
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#ef4444'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { usePointStyle: true, font: { weight: 'bold' } } }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { callback: (value) => value.toLocaleString() + ' /=' } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
