@extends('layouts.app')

@section('title', 'Login | GloryStock')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 90vh;">
    <div class="col-md-5">
        <div class="card p-5" style="max-width: 460px; margin: auto;">
            <div class="text-center mb-4">
                <img src="{{ asset('logo.png') }}" alt="Logo" style="height: 65px; width: auto; margin-bottom: 15px;">
                <h2 class="fw-bold text-dark mb-0">GLORYSTOCK</h2>
                <p class="text-muted small fw-bold">SIGN IN TO YOUR ACCOUNT</p>
                <div class="mx-auto mt-2" style="width: 40px; height: 3px; background: #3b82f6; border-radius: 2px;"></div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small text-center mb-4 border-0">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.attempt') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter username" value="{{ old('username') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="passwordField" class="form-control" placeholder="••••••••" required>
                        <span class="input-group-text bg-white" onclick="togglePassword()" style="cursor: pointer;">
                            <i class="fas fa-eye text-muted" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase">Login As</label>
                    <select name="role" class="form-select" required>
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff / Cashier</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-dark w-100 mb-4 fw-bold text-uppercase">Sign In</button>
            </form>

            <div class="text-center border-top pt-3">
                <p class="text-muted small mb-0">
                    Don't have an account? <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">Register Here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('passwordField');
        const eyeIcon = document.getElementById('eyeIcon');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
@endsection
