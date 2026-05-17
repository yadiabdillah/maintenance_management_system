@extends('layouts.app')

@section('title', 'Inventori Sparepart')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">Inventori Sparepart</h1>
        <p class="text-muted mb-0">Kelola master suku cadang dan pantau level persediaan secara real-time.</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('spareparts.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Master Sparepart
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filter Panel -->
<div class="card shadow-sm border-0 mb-4 bg-white rounded-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('spareparts.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari SKU atau Nama Sparepart..." value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-check form-switch pt-1">
                    <input class="form-check-input" type="checkbox" role="switch" id="lowStockSwitch" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="form-check-label fw-semibold text-danger" for="lowStockSwitch">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Hanya Tampilkan Stok Menipis / Habis
                    </label>
                </div>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                @if(request()->anyFilled(['search', 'low_stock']))
                    <a href="{{ route('spareparts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Spareparts Catalog List -->
<div class="card shadow-sm border-0 rounded-3 overflow-hidden bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" class="ps-4">No</th>
                        <th scope="col">SKU / Kode Part</th>
                        <th scope="col">Nama Barang</th>
                        <th scope="col">Stok Saat Ini</th>
                        <th scope="col">Min. Stok Alert</th>
                        <th scope="col">Status Stok</th>
                        <th scope="col" class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($spareparts as $index => $part)
                        <tr>
                            <td class="ps-4 text-muted">{{ $spareparts->firstItem() + $index }}</td>
                            <td>
                                <span class="badge bg-secondary font-monospace p-2" style="font-size: 0.85rem;">
                                    {{ $part->sku }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $part->name }}</div>
                            </td>
                            <td>
                                <span class="fw-bold fs-6 {{ $part->stock <= $part->min_stock ? 'text-danger' : 'text-dark' }}">
                                    {{ $part->stock }} pcs
                                </span>
                            </td>
                            <td class="text-muted font-monospace">{{ $part->min_stock }} pcs</td>
                            <td>
                                @if ($part->stock == 0)
                                    <span class="badge bg-dark px-3 py-2 rounded-pill"><i class="bi bi-x-circle me-1"></i> Habis (Kosong)</span>
                                @elseif ($part->stock <= $part->min_stock)
                                    <span class="badge bg-danger px-3 py-2 rounded-pill"><i class="bi bi-exclamation-triangle me-1"></i> Menipis</span>
                                @else
                                    <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle me-1"></i> Cukup</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm" role="group">
                                    <a href="{{ route('spareparts.show', $part->id) }}" class="btn btn-sm btn-light border" title="Mutasi Keluar Masuk / Buku Besar">
                                        <i class="bi bi-card-list text-primary"></i> <span class="small ms-1 d-none d-md-inline">Kartu Stok</span>
                                    </a>
                                    <a href="{{ route('spareparts.edit', $part->id) }}" class="btn btn-sm btn-light border" title="Edit Master Data">
                                        <i class="bi bi-pencil text-warning"></i>
                                    </a>
                                    <form action="{{ route('spareparts.destroy', $part->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus master sparepart ini? Seluruh riwayat transaksi terkait juga akan terhapus!');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border" title="Hapus">
                                            <i class="bi bi-trash text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                <h5 class="fw-semibold">Belum Ada Sparepart Terdaftar</h5>
                                <p class="small text-muted">Mulai dengan menambahkan master sparepart baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($spareparts->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">Menampilkan {{ $spareparts->firstItem() }} - {{ $spareparts->lastItem() }} dari {{ $spareparts->total() }} sparepart</span>
                <div>{{ $spareparts->links() }}</div>
            </div>
        </div>
    @endif
</div>
@endsection
