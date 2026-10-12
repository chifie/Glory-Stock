@extends('layouts.app')

@section('title', 'Register | GloryStock')

@section('content')
<div class="flex justify-center items-center min-h-[85vh]">
    <div class="card w-full max-w-md bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-extrabold text-slate-900 mb-1">Create Account</h2>
            <p class="text-slate-500 text-sm">Join the GloryStock team</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            <div class="mb-4">
                <label for="username" class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Choose Username</label>
                <input type="text" name="username" id="username" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none" value="{{ old('username') }}" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Set Password</label>
                <input type="password" name="password" id="password" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none" required>
            </div>

            <div class="mb-4">
                <label for="role" class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Your Role</label>
                <select name="role" id="roleSelect" onchange="checkRole()" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                    <option value="staff" {{ old('role', 'staff') === 'staff' ? 'selected' : '' }}>Staff (Sales Only)</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator (Full Access)</option>
                </select>
            </div>

            <div class="mb-5 hidden" id="adminSec">
                <label for="admin_key" class="block text-xs font-bold text-red-600 uppercase tracking-wide mb-1">Admin Secret Key</label>
                <input type="password" name="admin_key" id="admin_key" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none" placeholder="Enter security code">
            </div>

            <button type="submit" class="w-full mb-3 rounded-lg bg-slate-900 py-2.5 font-bold text-white hover:bg-slate-800 transition">Register Account</button>

            <p class="text-center text-sm text-slate-500">
                Already have an account? <a href="{{ route('login') }}" class="font-bold text-slate-900 hover:underline">Login</a>
            </p>
        </form>
    </div>
</div>

<script>
    function checkRole() {
        const role = document.getElementById('roleSelect').value;
        const adminSec = document.getElementById('adminSec');
        if (role === 'admin') {
            adminSec.classList.remove('hidden');
        } else {
            adminSec.classList.add('hidden');
        }
    }
</script>
@endsection
