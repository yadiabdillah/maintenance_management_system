@extends('layouts.app')

@section('title', 'Profil Saya - MMS')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-person-circle me-2 text-primary"></i>Profil Saya
        </h1>
        <p class="text-muted mb-0">Kelola informasi profil dan foto akun Anda.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Profile Card Left -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-3 text-center bg-white p-4">
            <div class="card-body">
                @if($user->avatar)
                    <img src="{{ asset('storage/avatars/' . $user->avatar) }}" alt="Avatar" 
                        width="120" height="120" class="rounded-circle border mb-3 shadow-sm object-fit-cover"
                        style="object-fit: cover;">
                @else
                    @php
                        $avatarBg = '6c757d';
                        if ($user->role === 'Super Admin') {
                            $avatarBg = 'dc3545';
                        } elseif ($user->role === 'Supervisor') {
                            $avatarBg = 'ffc107';
                        }
                        $avatarColor = $user->role === 'Supervisor' ? '000' : 'fff';
                    @endphp
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background={{ $avatarBg }}&color={{ $avatarColor }}&bold=true&size=120" alt="Avatar" width="120" height="120" class="rounded-circle border mb-3 shadow-sm">
                @endif
                
                <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-3 font-monospace small">{{ $user->email }}</p>

                <div class="mb-3">
                    @if ($user->role === 'Super Admin')
                        <span class="badge bg-danger px-3 py-2 rounded-pill fs-6"><i class="bi bi-shield-lock-fill me-1"></i> Super Admin</span>
                    @elseif ($user->role === 'Supervisor')
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6"><i class="bi bi-person-badge-fill me-1"></i> Supervisor</span>
                    @else
                        <span class="badge bg-success px-3 py-2 rounded-pill fs-6"><i class="bi bi-person-fill me-1"></i> Operator</span>
                    @endif
                </div>

                <div class="d-flex flex-column gap-2 mt-4">
                    <a href="{{ route('profile.photo') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-camera me-1"></i> Ubah Foto
                    </a>
                    <a href="{{ route('profile.password') }}" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-key me-1"></i> Ubah Password
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Card Right -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-3 bg-white p-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Profil</h5>
                    <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg me-1"></i> Batal
                    </a>
                </div>
                
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light border-0 @error('name') is-invalid @enderror" 
                            id="name" name="name" placeholder="Contoh: Budi Setiawan" required 
                            value="{{ old('name', $user->name) }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold text-dark">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control bg-light border-0 @error('email') is-invalid @enderror" 
                            id="email" name="email" placeholder="Contoh: budi@mms.com" required 
                            value="{{ old('email', $user->email) }}">
                        <div class="form-text small text-muted">Email digunakan untuk login ke sistem.</div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Account Info -->
        <div class="card shadow-sm border-0 rounded-3 bg-white p-4 mt-4">
            <div class="card-body">
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <i class="bi bi-info-circle me-2 text-secondary"></i>Informasi Akun
                </h5>
                
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted fw-semibold">ID Pengguna</div>
                    <div class="col-sm-8 text-dark font-monospace">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted fw-semibold">Terdaftar Pada</div>
                    <div class="col-sm-8 text-dark">{{ $user->created_at ? $user->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</div>
                </div>
                
                <div class="row">
                    <div class="col-sm-4 text-muted fw-semibold">Terakhir Diubah</div>
                    <div class="col-sm-8 text-dark">{{ $user->updated_at ? $user->updated_at->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection