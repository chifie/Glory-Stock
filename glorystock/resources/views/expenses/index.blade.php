@extends('layouts.app')

@section('title', 'Expense Tracker | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="d-flex align-items-center mb-4 border-bottom pb-3">
    <img src="{{ asset('logo.png') }}" alt="GloryStock" class="page-logo">
    <div>
        <h3 class="fw-800 mb-0">Expense Management</h3>
        <p class="text-muted small mb-0">Track and record business outgoings</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card p-4">
            <h5 class="fw-bold mb-4">Record New Expense</h5>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small">❌ {{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('expenses.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Category</label>
                    <select name="category" class="form-select" required>
                        @foreach (\App\Models\Expense::CATEGORIES as $categoryOption)
                            <option value="{{ $categoryOption }}" {{ old('category') === $categoryOption ? 'selected' : '' }}>{{ $categoryOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Amount (TZS)</label>
                    <div class="input-group">
                        <input type="number" name="amount" class="form-control" placeholder="e.g. 50000" step="0.01" min="0.01" value="{{ old('amount') }}" required>
                        <span class="input-group-text bg-white small fw-bold">/=</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Date</label>
                    <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="What was this for?">{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="btn btn-dark text-white w-100 py-3 shadow-sm fw-bold">
                    <i class="fas fa-save me-2"></i> Save to Records
                </button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card h-100 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Recent Outgoings</h5>
                <span class="badge bg-light text-dark border fw-bold px-3 py-2">{{ $expenses->count() }} Total Items</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Category</th>
                            <th>Details</th>
                            <th class="text-end pe-4">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr>
                                <td class="ps-4 small text-muted">
                                    <i class="far fa-calendar-alt me-1"></i> {{ $expense->expense_date->format('M d, Y') }}
                                </td>
                                <td>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                        {{ $expense->category }}
                                    </span>
                                </td>
                                <td class="small text-dark fw-medium">{{ $expense->description }}</td>
                                <td class="text-end pe-4 fw-bold text-dark">{{ number_format((float) $expense->amount) }} /=</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted">No expenses recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
