@extends('layouts.app')

@section('title', 'Ubah Password - MMS')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('profile.show') }}">Profil Saya</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ubah Password</li>
            </ol>
        </nav>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-key-fill me-2 text-warning"></i>Ubah Password
        </h1>
        <p class="text-muted mb-0">Pastikan password baru Anda kuat dan mudah diingat.</p>
    </div>
    <div>
        <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3 bg-white p-4" style="max-width: 500px;">
    <div class="card-body">
        <form action="{{ route('profile.password.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="current_password" class="form-label fw-semibold text-dark">
                    Password Saat Ini <span class="text-danger">*</span>
                </label>
                <input type="password" class="form-control bg-light border-0 @error('current_password') is-invalid @enderror" 
                    id="current_password" name="current_password" placeholder="Masukkan password saat ini" required
                    autocomplete="current-password">
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr class="my-4">

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold text-dark">
                    Password Baru <span class="text-danger">*</span>
                </label>
                <input type="password" class="form-control bg-light border-0 @error('password') is-invalid @enderror" 
                    id="password" name="password" placeholder="Minimal 6 karakter" required
                    autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label fw-semibold text-dark">
                    Konfirmasi Password Baru <span class="text-danger">*</span>
                </label>
                <input type="password" class="form-control bg-light border-0" 
                    id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru" required
                    autocomplete="new-password">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning px-4 fw-semibold shadow-sm text-dark">
                    <i class="bi bi-shield-lock me-1"></i> Ubah Password
                </button>
                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection