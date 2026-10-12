@extends('layouts.app')

@section('title', 'Login | GloryStock')

@section('content')
<div class="flex justify-center items-center min-h-[85vh]">
    <div class="card w-full max-w-md bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-6">
            <img src="{{ asset('logo.png') }}" alt="Logo" class="h-16 w-auto mx-auto mb-4">
            <h2 class="text-2xl font-extrabold text-slate-900 mb-0">GLORYSTOCK</h2>
            <p class="text-slate-500 text-xs font-bold uppercase tracking-wide mt-1">Sign in to your account</p>
            <div class="mx-auto mt-2 h-1 w-10 rounded bg-blue-500"></div>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-center text-small text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.attempt') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="username" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Username</label>
                <input type="text" name="username" id="username" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none" placeholder="Enter username" value="{{ old('username') }}" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Password</label>
                <div class="relative flex items-center border border-slate-300 rounded-lg focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-200">
                    <input type="password" name="password" id="passwordField" class="w-full rounded-lg px-3 py-2 focus:outline-none" placeholder="••••••••" required>
                    <button type="button" onclick="togglePassword()" class="px-3 text-slate-400 hover:text-slate-600">
                        <span id="eyeIcon">&#128065;</span>
                    </button>
                </div>
            </div>

            <div class="mb-5">
                <label for="role" class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Login As</label>
                <select name="role" id="role" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none" required>
                    <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff / Cashier</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>
            </div>

            <button type="submit" class="w-full mb-4 rounded-lg bg-slate-900 py-2.5 font-bold uppercase text-white tracking-wide hover:bg-slate-800 transition">Sign In</button>
        </form>

        <div class="text-center border-t border-slate-200 pt-4">
            <p class="text-slate-500 text-sm mb-0">
                Don't have an account? <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:underline">Register Here</a>
            </p>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('passwordField');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
        } else {
            passwordField.type = 'password';
        }
    }
</script>
@endsection
