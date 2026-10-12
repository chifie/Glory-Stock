@extends('layouts.app')

@section('title', 'Business Reports | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="flex justify-between items-center mb-6 border-b border-slate-200 pb-4">
    <div class="flex items-center gap-4">
        <img src="{{ asset('logo.png') }}" alt="GloryStock" class="h-11 w-auto object-contain">
        <div>
            <h3 class="text-xl font-extrabold mb-0">Business Analytics</h3>
            <p class="text-slate-500 text-sm mb-0">Financial overview for the last 6 months</p>
        </div>
    </div>
    <button onclick="window.print()" class="rounded-full bg-slate-900 px-5 py-2 font-bold text-white hover:bg-slate-800 transition">
        &#128424;&#65039; Print Report
    </button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
    <div class="lg:col-span-12">
        <div class="rounded-xl bg-white shadow-md p-5">
            <div class="flex justify-between items-center mb-3">
                <h5 class="font-bold mb-0">Revenue vs Expenses</h5>
                <div class="text-sm text-slate-500">Real-time Performance Graph</div>
            </div>
            <div style="height: 350px;">
                <canvas id="businessChart"></canvas>
            </div>
        </div>
    </div>

    <div class="lg:col-span-7">
        <div class="rounded-xl bg-white shadow-md p-5 h-full">
            <h6 class="text-xs font-bold uppercase text-slate-500 mb-4">Monthly Statement</h6>
            <div class="overflow-x-auto">
                <table class="w-full text-left align-middle">
                    <thead class="text-xs uppercase text-slate-400">
                        <tr>
                            <th class="py-2">Month</th>
                            <th class="py-2">Revenue</th>
                            <th class="py-2">Expenses</th>
                            <th class="py-2">Net Profit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($months as $index => $month)
                            @php($revenue = $revenueValues[$index])
                            @php($expense = $expenseValues[$index])
                            @php($profit = $revenue - $expense)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2.5 font-bold text-slate-900">{{ $month }}</td>
                                <td class="py-2.5 font-semibold text-emerald-600">{{ number_format($revenue) }} /=</td>
                                <td class="py-2.5 text-red-600">{{ number_format($expense) }} /=</td>
                                <td class="py-2.5 font-bold {{ $profit >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                    {{ ($profit < 0 ? '-' : '+').number_format(abs($profit)) }} /=
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="lg:col-span-5">
        <div class="rounded-xl bg-white shadow-md p-5 h-full">
            <h6 class="text-xs font-bold uppercase text-slate-500 mb-4">Best Selling Products</h6>
            @forelse ($topProducts as $item)
                <div class="flex justify-between items-center mb-3 border-b border-slate-100 pb-2">
                    <span class="font-bold text-slate-900">{{ $item->name }}</span>
                    <span class="rounded-full bg-blue-600 px-3 py-1 text-xs font-semibold text-white">{{ $item->total_sold }} Units Sold</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No sales data yet.</p>
            @endforelse

            <div class="mt-5">
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-center text-sm text-slate-600">
                    &#8505;&#65039; These items generate the highest footfall for GloryStock.
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
