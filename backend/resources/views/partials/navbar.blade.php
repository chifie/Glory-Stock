<nav class="bg-slate-900 shadow-md mb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 font-extrabold tracking-wide text-white">
                <img src="{{ asset('logo.png') }}" alt="GloryStock" class="h-9 w-auto object-contain">
                <span class="tracking-wider">GLORYSTOCK</span>
            </a>

            <div class="flex items-center gap-1">
                <a href="{{ route('pos.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition {{ request()->routeIs('pos.*') ? 'bg-slate-800 text-white' : '' }}">Point of Sale</a>
                <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : '' }}">Inventory</a>
                <a href="{{ route('sales.history') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition {{ request()->routeIs('sales.history') ? 'bg-slate-800 text-white' : '' }}">Sales History</a>

                @if (auth()->user()->isAdmin())
                    <a href="{{ route('reports') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-sky-400 hover:text-sky-300 hover:bg-slate-800 transition {{ request()->routeIs('reports') ? 'bg-slate-800 text-sky-300' : '' }}">Reports</a>
                    <a href="{{ route('expenses.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-sky-400 hover:text-sky-300 hover:bg-slate-800 transition {{ request()->routeIs('expenses.*') ? 'bg-slate-800 text-sky-300' : '' }}">Expenses</a>
                    <a href="{{ route('users.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold text-amber-400 hover:text-amber-300 hover:bg-slate-800 transition {{ request()->routeIs('users.*') ? 'bg-slate-800 text-amber-300' : '' }}">Manage Staff</a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="ml-2">
                    @csrf
                    <button type="submit" class="px-3 py-2 rounded-lg text-sm font-bold text-red-400 hover:text-red-300 hover:bg-slate-800 transition">
                        &larr; Logout ({{ auth()->user()->username }})
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
