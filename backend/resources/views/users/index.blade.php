@extends('layouts.app')

@section('title', 'Manage Staff | GloryStock')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
<div class="flex items-center gap-4 mb-6 border-b border-slate-200 pb-4">
    <img src="{{ asset('logo.png') }}" alt="GloryStock" class="h-11 w-auto object-contain">
    <div>
        <h3 class="text-xl font-extrabold mb-0">Staff Management</h3>
        <p class="text-slate-500 text-sm mb-0">Control system access and authorized personnel</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-4">
        <div class="rounded-xl bg-white shadow-md p-5">
            <h5 class="font-bold mb-5">Register New Staff</h5>

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700">&#10060; {{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="username" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">&#128100;</span>
                        <input type="text" name="username" id="username" class="w-full rounded-lg border border-slate-300 pl-9 pr-3 py-2 focus:border-blue-500 focus:outline-none" placeholder="e.g. bakari_m" value="{{ old('username') }}" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">&#128274;</span>
                        <input type="password" name="password" id="password" class="w-full rounded-lg border border-slate-300 pl-9 pr-3 py-2 focus:border-blue-500 focus:outline-none" placeholder="••••••••" required>
                    </div>
                </div>
                <div class="mb-5">
                    <label for="role" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Access Level</label>
                    <select name="role" id="role" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                        <option value="staff">Staff (Sales Only)</option>
                        <option value="admin">Administrator (Full Access)</option>
                    </select>
                </div>
                <button type="submit" class="w-full rounded-lg bg-slate-900 py-3 shadow-sm font-bold text-white hover:bg-slate-800 transition">
                    &#10133; Create Account
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-8">
        <div class="rounded-xl bg-white shadow-md overflow-hidden h-full">
            <div class="flex justify-between items-center border-b border-slate-200 bg-white px-5 py-3">
                <h5 class="mb-0 font-bold">Authorized Personnel</h5>
                <span class="rounded-full border border-slate-200 bg-slate-100 px-3 py-1.5 text-xs font-bold">{{ $users->count() }} Users</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left align-middle">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="pl-5 py-3">ID</th>
                            <th class="py-3">Username</th>
                            <th class="py-3">Role</th>
                            <th class="pr-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($users as $user)
                            <tr class="hover:bg-slate-50">
                                <td class="pl-5 py-3 text-slate-500 text-sm">#{{ $user->id }}</td>
                                <td class="py-3 font-bold text-slate-900">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                            &#128100;
                                        </div>
                                        {{ $user->username }}
                                    </div>
                                </td>
                                <td class="py-3">
                                    @if ($user->role === 'admin')
                                        <span class="rounded-full border border-blue-200 bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">Admin</span>
                                    @else
                                        <span class="rounded-full border border-slate-200 bg-slate-100 px-2.5 py-0.5 text-xs">Staff</span>
                                    @endif
                                </td>
                                <td class="pr-5 py-3 text-right">
                                    @if ($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm('Revoke all access for this staff member?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-full border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100 transition">
                                                &#128396;&#65039; Revoke Access
                                            </button>
                                        </form>
                                    @else
                                        <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                                            &#9989; You (Active)
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
