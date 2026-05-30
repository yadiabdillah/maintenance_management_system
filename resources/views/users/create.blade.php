@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen Pengguna</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Pengguna</li>
            </ol>
        </nav>
        <h1 class="h2 fw-bold text-dark mb-0">Tambah Pengguna Baru</h1>
        <p class="text-muted mb-0">Daftarkan akun pengguna sistem MMS baru dengan hak akses spesifik.</p>
    </div>
    <div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-x-lg me-1"></i> Batal
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3 bg-white p-4" style="max-width: 600px;">
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control bg-light border-0 @error('name') is-invalid @enderror" id="name" name="name" placeholder="Contoh: Budi Setiawan" required value="{{ old('name') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-dark">Alamat Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control bg-light border-0 @error('email') is-invalid @enderror" id="email" name="email" placeholder="Contoh: budi@mms.com" required value="{{ old('email') }}">
                <div class="form-text small text-muted">Email ini akan digunakan untuk login sistem.</div>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold text-dark">Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control bg-light border-0 @error('password') is-invalid @enderror" id="password" name="password" placeholder="Minimal 6 karakter" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label fw-semibold text-dark">Konfirmasi Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control bg-light border-0" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label fw-semibold text-dark">Peran / Role Akses <span class="text-danger">*</span></label>
                <select name="role" id="role" class="form-select bg-light border-0 @error('role') is-invalid @enderror" required>
                    <option value="" disabled selected>-- Pilih Role --</option>
                    <option value="Super Admin" {{ old('role') === 'Super Admin' ? 'selected' : '' }}>Super Admin (Akses Penuh)</option>
                    <option value="Supervisor" {{ old('role') === 'Supervisor' ? 'selected' : '' }}>Supervisor (Koordinator/Monitoring)</option>
                    <option value="Operator" {{ old('role') === 'Operator' ? 'selected' : '' }}>Operator (Mekanik/Gudang/Mesin)</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-dark d-block">Status Akun</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', '1') === '1' ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold text-success" for="is_active" id="statusLabel">Aktif</label>
                </div>
                <div class="form-text small text-muted">Pengguna non-aktif tidak akan bisa melakukan login ke sistem.</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                    <i class="bi bi-save me-1"></i> Simpan Pengguna
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const activeSwitch = document.getElementById('is_active');
        const statusLabel = document.getElementById('statusLabel');

        function updateLabel() {
            if (activeSwitch.checked) {
                statusLabel.textContent = 'Aktif';
                statusLabel.className = 'form-check-label fw-semibold text-success';
            } else {
                statusLabel.textContent = 'Non-Aktif';
                statusLabel.className = 'form-check-label fw-semibold text-danger';
            }
        }

        activeSwitch.addEventListener('change', updateLabel);
        updateLabel();
    });
</script>
@endpush
