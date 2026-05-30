@extends('layouts.app')

@section('title', 'Detail Pengguna')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen Pengguna</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
            </ol>
        </nav>
        <h1 class="h2 fw-bold text-dark mb-0">Detail Profil Pengguna</h1>
        <p class="text-muted mb-0">Informasi lengkap mengenai biodata, peran, dan status akun.</p>
    </div>
    <div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
</div>

<div class="row g-4" style="max-width: 800px;">
    <!-- Profile Card Left -->
    <div class="col-md-5">
        <div class="card shadow-sm border-0 rounded-3 text-center bg-white p-4 h-100">
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
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

                <div>
                    @if ($user->is_active)
                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill border border-success"><i class="bi bi-check-circle-fill me-1"></i> Akun Aktif</span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill border border-secondary"><i class="bi bi-x-circle-fill me-1"></i> Akun Non-Aktif</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Details Card Right -->
    <div class="col-md-7">
        <div class="card shadow-sm border-0 rounded-3 bg-white p-4 h-100">
            <div class="card-body">
                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2"><i class="bi bi-info-circle me-1 text-primary"></i> Data Administratif</h5>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted fw-semibold">ID Pengguna</div>
                    <div class="col-sm-8 text-dark font-monospace">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted fw-semibold">Nama Lengkap</div>
                    <div class="col-sm-8 text-dark fw-bold">{{ $user->name }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted fw-semibold">Alamat Email</div>
                    <div class="col-sm-8 text-dark font-monospace">{{ $user->email }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted fw-semibold">Peran Sistem</div>
                    <div class="col-sm-8 text-dark">
                        @if($user->role === 'Super Admin')
                            Super Admin (Akses Kontrol & Master Data)
                        @elseif($user->role === 'Supervisor')
                            Supervisor (Monitoring, SLA, & Assign Tugas)
                        @else
                            Operator (Mekanik / Gudang / Produksi)
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted fw-semibold">Terdaftar Pada</div>
                    <div class="col-sm-8 text-dark">{{ $user->created_at ? $user->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-4 text-muted fw-semibold">Terakhir Diubah</div>
                    <div class="col-sm-8 text-dark">{{ $user->updated_at ? $user->updated_at->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</div>
                </div>

                <div class="d-flex gap-2 pt-3 border-top">
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning px-4 fw-semibold text-dark shadow-sm">
                        <i class="bi bi-pencil me-1"></i> Edit Pengguna
                    </a>
                    
                    @if(auth()->id() !== $user->id)
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.');" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger px-4 fw-semibold">
                                <i class="bi bi-trash me-1"></i> Hapus Pengguna
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
