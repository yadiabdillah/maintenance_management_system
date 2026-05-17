@extends('layouts.guest')

@section('title', 'Login - MMS')

@section('content')
<div class="card shadow-sm" style="width: 100%; max-width: 400px; border-radius: 1rem;">
    <div class="card-body p-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold">MMS Login</h2>
            <p class="text-muted">Masuk untuk mengelola Maintenance</p>
        </div>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="floatingInput" name="email" placeholder="name@example.com" required>
                <label for="floatingInput">Email address</label>
            </div>
            
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required>
                <label for="floatingPassword">Password</label>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" id="rememberMe" name="remember">
                <label class="form-check-label" for="rememberMe">
                    Remember me
                </label>
            </div>

            <button class="w-100 btn btn-lg btn-primary" type="submit">Log In</button>
            
            <div class="text-center mt-3">
                <a href="#" class="text-decoration-none small">Forgot password?</a>
            </div>
        </form>
    </div>
</div>
@endsection
