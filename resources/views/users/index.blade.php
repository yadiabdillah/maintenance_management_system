@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">Manajemen Pengguna</h1>
        <p class="text-muted mb-0">Kelola akun pengguna, peran (role), dan hak akses sistem.</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('users.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna Baru
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filter Panel -->
<div class="card shadow-sm border-0 mb-4 bg-white rounded-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="col-md-3">
                <select name="role" class="form-select bg-light border-0" onchange="this.form.submit()">
                    <option value="">-- Semua Role --</option>
                    <option value="Super Admin" {{ request('role') == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="Supervisor" {{ request('role') == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                    <option value="Operator" {{ request('role') == 'Operator' ? 'selected' : '' }}>Operator (Mekanik/Gudang/Mesin)</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="is_active" class="form-select bg-light border-0" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                @if(request()->anyFilled(['search', 'role', 'is_active']))
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card shadow-sm border-0 rounded-3 overflow-hidden bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" class="ps-4">No</th>
                        <th scope="col">Nama Pengguna</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $index => $user)
                        <tr>
                            <td class="ps-4 text-muted">{{ $users->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @php
                                        // Pilih warna avatar berdasarkan role
                                        $avatarBg = '6c757d'; // default operator
                                        if ($user->role === 'Super Admin') {
                                            $avatarBg = 'dc3545'; // merah
                                        } elseif ($user->role === 'Supervisor') {
                                            $avatarBg = 'ffc107'; // kuning/emas
                                        }
                                        $avatarColor = $user->role === 'Supervisor' ? '000' : 'fff';
                                    @endphp
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background={{ $avatarBg }}&color={{ $avatarColor }}&bold=true" alt="" width="36" height="36" class="rounded-circle me-3 border">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                        @if(auth()->id() === $user->id)
                                            <span class="badge bg-light text-dark border small" style="font-size: 0.7rem;">Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted font-monospace">{{ $user->email }}</td>
                            <td>
                                @if ($user->role === 'Super Admin')
                                    <span class="badge bg-danger px-3 py-2 rounded-pill"><i class="bi bi-shield-lock-fill me-1"></i> Super Admin</span>
                                @elseif ($user->role === 'Supervisor')
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="bi bi-person-badge-fill me-1"></i> Supervisor</span>
                                @else
                                    <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-person-fill me-1"></i> Operator</span>
                                @endif
                            </td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill border border-success"><i class="bi bi-check-circle-fill me-1"></i> Aktif</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill border border-secondary"><i class="bi bi-x-circle-fill me-1"></i> Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm" role="group">
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-light border" title="Detail Pengguna">
                                        <i class="bi bi-eye text-primary"></i> <span class="small ms-1 d-none d-md-inline">Detail</span>
                                    </a>
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-light border" title="Edit Data">
                                        <i class="bi bi-pencil text-warning"></i>
                                    </a>
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border" title="Hapus">
                                                <i class="bi bi-trash text-danger"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-sm btn-light border disabled" title="Tidak dapat menghapus diri sendiri">
                                            <i class="bi bi-trash text-muted"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                <h5 class="fw-semibold">Belum Ada Pengguna</h5>
                                <p class="small text-muted font-monospace">Gunakan tombol di atas untuk menambah pengguna baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} pengguna</span>
                <div>{{ $users->links() }}</div>
            </div>
        </div>
    @endif
</div>
@endsection
