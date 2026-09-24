<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold" href="{{ route('dashboard') }}">
            <img src="{{ asset('logo.png') }}" alt="GloryStock" style="height: 38px; width: auto; margin-right: 12px; object-fit: contain;">
            <span style="letter-spacing: 0.5px;">GLORYSTOCK</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto align-items-center">
                <a class="nav-link px-3 {{ request()->routeIs('pos.*') ? 'active' : '' }}" href="{{ route('pos.index') }}">Point of Sale</a>
                <a class="nav-link px-3 {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Inventory</a>
                <a class="nav-link px-3 {{ request()->routeIs('sales.history') ? 'active' : '' }}" href="{{ route('sales.history') }}">Sales History</a>

                @if (auth()->user()->isAdmin())
                    <a class="nav-link px-3 text-info {{ request()->routeIs('reports') ? 'active' : '' }}" href="{{ route('reports') }}">Reports</a>
                    <a class="nav-link px-3 text-info {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}">Expenses</a>
                    <a class="nav-link px-3 text-warning fw-bold {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">Manage Staff</a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link text-danger ms-lg-3 fw-bold">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout ({{ auth()->user()->username }})
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
