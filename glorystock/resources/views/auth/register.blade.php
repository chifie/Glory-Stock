@extends('layouts.app')

@section('title', 'Register | GloryStock')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 90vh;">
    <div class="col-md-5">
        <div class="card p-5" style="max-width: 440px; margin: auto;">
            <div class="text-center mb-4">
                <h2 class="fw-bold">Create Account</h2>
                <p class="text-muted">Join the GloryStock team</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small mb-4 border-0">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="small fw-bold">Choose Username</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold">Set Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="passReg" class="form-control" required>
                        <span class="input-group-text" onclick="togglePass()" style="cursor: pointer;">
                            <i class="fa-solid fa-eye" id="eyeReg"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold">Your Role</label>
                    <select name="role" class="form-select" id="roleSelect" onchange="checkRole()">
                        <option value="staff" {{ old('role', 'staff') === 'staff' ? 'selected' : '' }}>Staff (Sales Only)</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator (Full Access)</option>
                    </select>
                </div>

                <div class="mb-4" id="adminSec" style="display: none;">
                    <label class="small fw-bold text-danger">Admin Secret Key</label>
                    <input type="password" name="admin_key" class="form-control" placeholder="Enter security code">
                </div>

                <button type="submit" class="btn btn-dark w-100 mb-3 text-white">Register Account</button>

                <p class="text-center small text-muted">
                    Already have an account? <a href="{{ route('login') }}" class="fw-bold text-dark text-decoration-none">Login</a>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
    function checkRole() {
        const role = document.getElementById('roleSelect').value;
        document.getElementById('adminSec').style.display = (role === 'admin') ? 'block' : 'none';
    }
    function togglePass() {
        const input = document.getElementById('passReg');
        const eye = document.getElementById('eyeReg');
        if (input.type === 'password') {
            input.type = 'text';
            eye.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            eye.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
@endsection
