@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen Pengguna</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.show', $user->id) }}">{{ $user->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
        <h1 class="h2 fw-bold text-dark mb-0">Edit Pengguna</h1>
        <p class="text-muted mb-0">Ubah data profil, role, atau status aktifasi pengguna.</p>
    </div>
    <div>
        <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-x-lg me-1"></i> Batal
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3 bg-white p-4" style="max-width: 600px;">
    <div class="card-body">
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control bg-light border-0 @error('name') is-invalid @enderror" id="name" name="name" placeholder="Contoh: Budi Setiawan" required value="{{ old('name', $user->name) }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-dark">Alamat Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control bg-light border-0 @error('email') is-invalid @enderror" id="email" name="email" placeholder="Contoh: budi@mms.com" required value="{{ old('email', $user->email) }}">
                <div class="form-text small text-muted">Email ini digunakan untuk login sistem.</div>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 bg-light-subtle p-3 rounded border">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-key-fill me-1"></i> Ubah Password (Opsional)</h6>
                <div class="mb-2">
                    <label for="password" class="form-label small fw-semibold text-dark">Password Baru</label>
                    <input type="password" class="form-control bg-light border-0 @error('password') is-invalid @enderror" id="password" name="password" placeholder="Minimal 6 karakter">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="form-label small fw-semibold text-dark">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control bg-light border-0" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru">
                </div>
                <div class="form-text small text-muted mt-2">Biarkan kosong jika tidak ingin mengubah password pengguna.</div>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label fw-semibold text-dark">Peran / Role Akses <span class="text-danger">*</span></label>
                <select name="role" id="role" class="form-select bg-light border-0 @error('role') is-invalid @enderror" required>
                    <option value="Super Admin" {{ old('role', $user->role) === 'Super Admin' ? 'selected' : '' }}>Super Admin (Akses Penuh)</option>
                    <option value="Supervisor" {{ old('role', $user->role) === 'Supervisor' ? 'selected' : '' }}>Supervisor (Koordinator/Monitoring)</option>
                    <option value="Operator" {{ old('role', $user->role) === 'Operator' ? 'selected' : '' }}>Operator (Mekanik/Gudang/Mesin)</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-dark d-block">Status Akun</label>
                @if(auth()->id() === $user->id)
                    <div class="form-check form-switch opacity-75">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked disabled>
                        <input type="hidden" name="is_active" value="1">
                        <label class="form-check-label fw-semibold text-success" for="is_active">Aktif</label>
                    </div>
                    <div class="form-text small text-danger mt-1"><i class="bi bi-info-circle-fill me-1"></i> Anda tidak dapat menonaktifkan akun sendiri untuk mencegah terkunci dari sistem.</div>
                @else
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $user->is_active ? '1' : '0') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-success" for="is_active" id="statusLabel">Aktif</label>
                    </div>
                    <div class="form-text small text-muted">Pengguna non-aktif tidak akan bisa melakukan login ke sistem.</div>
                @endif
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning px-4 fw-semibold shadow-sm text-dark">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
                <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@if(auth()->id() !== $user->id)
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
@endif
@endpush
