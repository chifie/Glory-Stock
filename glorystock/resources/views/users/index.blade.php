@extends('layouts.app')

@section('title', 'Manage Staff | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="d-flex align-items-center mb-4 border-bottom pb-3">
    <img src="{{ asset('logo.png') }}" alt="GloryStock" class="page-logo">
    <div>
        <h3 class="fw-800 mb-0">Staff Management</h3>
        <p class="text-muted small mb-0">Control system access and authorized personnel</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card p-4">
            <h5 class="fw-bold mb-4">Register New Staff</h5>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small">❌ {{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                        <input type="text" name="username" class="form-control border-start-0" placeholder="e.g. bakari_m" value="{{ old('username') }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase">Access Level</label>
                    <select name="role" class="form-select">
                        <option value="staff">Staff (Sales Only)</option>
                        <option value="admin">Administrator (Full Access)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-dark text-white w-100 fw-bold py-3 shadow-sm">
                    <i class="fas fa-user-plus me-2"></i> Create Account
                </button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card h-100 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Authorized Personnel</h5>
                <span class="badge bg-light text-dark border px-3 py-2 fw-bold">{{ $users->count() }} Users</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td class="ps-4 text-muted small">#{{ $user->id }}</td>
                                <td class="fw-bold text-dark">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            <i class="fas fa-user-circle text-muted"></i>
                                        </div>
                                        {{ $user->username }}
                                    </div>
                                </td>
                                <td>
                                    @if ($user->role === 'admin')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Admin</span>
                                    @else
                                        <span class="badge bg-light text-dark border">Staff</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if ($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm('Revoke all access for this staff member?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold border-0">
                                                <i class="fas fa-user-slash me-1"></i> Revoke Access
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-success-subtle text-success fw-bold px-3 py-2 rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> You (Active)
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
