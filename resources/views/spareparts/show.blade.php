@extends('layouts.app')

@section('title', 'Kartu Stok - ' . $sparepart->name)

@push('styles')
<style>
    #transactionTab {
        gap: 8px;
    }
    
    #in-tab {
        color: #198754 !important;
        background-color: transparent !important;
        border: 1px solid #198754 !important;
        transition: all 0.2s ease-in-out;
    }
    #in-tab.active {
        color: #ffffff !important;
        background-color: #198754 !important;
        border-color: #198754 !important;
    }
    
    #out-tab {
        color: #dc3545 !important;
        background-color: transparent !important;
        border: 1px solid #dc3545 !important;
        transition: all 0.2s ease-in-out;
    }
    #out-tab.active {
        color: #ffffff !important;
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('spareparts.index') }}">Inventori Sparepart</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kartu Stok (Ledger)</li>
            </ol>
        </nav>
        <h1 class="h2 fw-bold text-dark mb-0">{{ $sparepart->name }}</h1>
        <p class="text-muted mb-0">SKU / Kode Part: <span class="font-monospace text-secondary fw-semibold">{{ $sparepart->sku }}</span></p>
    </div>
    <div>
        <a href="{{ route('spareparts.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <!-- LEFT PANEL: STOCK STATUS & ADJUSTMENT FORMS -->
    <div class="col-lg-4 mb-4">
        <!-- Stock Status Card -->
        <div class="card shadow-sm border-0 rounded-3 bg-white p-4 text-center mb-4">
            <div class="card-body p-0">
                <div class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded-circle mb-3 shadow-sm" style="width: 70px; height: 70px;">
                    <i class="bi bi-box-seam fs-2"></i>
                </div>
                <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">Stok Saat Ini</h6>
                <h1 class="display-4 fw-bold mb-3 text-dark">{{ $sparepart->stock }} <span class="fs-5 text-muted fw-normal">pcs</span></h1>
                
                <div class="d-flex justify-content-center gap-2 mb-4">
                    @if ($sparepart->stock == 0)
                        <span class="badge bg-dark px-3 py-2 rounded-pill fs-6"><i class="bi bi-x-circle me-1"></i> Habis (Kosong)</span>
                    @elseif ($sparepart->stock <= $sparepart->min_stock)
                        <span class="badge bg-danger px-3 py-2 rounded-pill fs-6"><i class="bi bi-exclamation-triangle me-1"></i> Stok Menipis</span>
                    @else
                        <span class="badge bg-success px-3 py-2 rounded-pill fs-6"><i class="bi bi-check-circle me-1"></i> Stok Cukup</span>
                    @endif
                </div>

                <div class="text-start bg-light p-3 rounded border">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Batas Min. Alert:</span>
                        <span class="fw-bold font-monospace">{{ $sparepart->min_stock }} pcs</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">ID SKU Sistem:</span>
                        <span class="fw-bold font-monospace small">#{{ $sparepart->id }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Transaction forms (IN & OUT) -->
        <div class="card shadow-sm border-0 rounded-3 bg-white">
            <div class="card-header bg-white border-0 pt-3">
                <ul class="nav nav-pills nav-fill" id="transactionTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="in-tab" data-bs-toggle="tab" data-bs-target="#in-form" type="button" role="tab" aria-controls="in-form" aria-selected="true">
                            <i class="bi bi-arrow-down-left me-1"></i> Barang Masuk (IN)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="out-tab" data-bs-toggle="tab" data-bs-target="#out-form" type="button" role="tab" aria-controls="out-form" aria-selected="false">
                            <i class="bi bi-arrow-up-right me-1"></i> Barang Keluar (OUT)
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content" id="transactionTabContent">
                    
                    <!-- TAB 1: BARANG MASUK (IN) -->
                    <div class="tab-pane fade show active" id="in-form" role="tabpanel" aria-labelledby="in-tab">
                        <form action="{{ route('spareparts.adjust', $sparepart->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="IN">
                            
                            <div class="mb-3">
                                <label for="in_qty" class="form-label fw-semibold text-dark">Jumlah Masuk (Pcs) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control bg-light border-0 font-monospace" id="in_qty" name="qty" placeholder="Jumlah pcs" required min="1">
                            </div>

                            <div class="mb-3">
                                <label for="supplier_name" class="form-label fw-semibold text-dark">Supplier <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light border-0" id="supplier_name" name="supplier_name" placeholder="Nama Supplier / Vendor" required>
                                <div class="form-text small text-muted">Bisa dari 2 supplier berbeda, sejarah pembelian akan tercatat di log.</div>
                            </div>

                            <div class="mb-3">
                                <label for="unit_price" class="form-label fw-semibold text-dark">Harga Satuan (Rp - Opsional)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">Rp</span>
                                    <input type="number" class="form-control bg-light border-0 font-monospace" id="unit_price" name="unit_price" placeholder="Contoh: 15000">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="in_remarks" class="form-label fw-semibold text-dark">Catatan / No. PO / Referensi</label>
                                <textarea class="form-control bg-light border-0" id="in_remarks" name="remarks" rows="3" placeholder="Contoh: Restock barang, Invoice #10294"></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100 fw-bold shadow-sm">
                                <i class="bi bi-plus-lg me-1"></i> Simpan Stok Masuk
                            </button>
                        </form>
                    </div>

                    <!-- TAB 2: BARANG KELUAR (OUT) -->
                    <div class="tab-pane fade" id="out-form" role="tabpanel" aria-labelledby="out-tab">
                        <form action="{{ route('spareparts.adjust', $sparepart->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="OUT">
                            
                            <div class="mb-3">
                                <label for="out_qty" class="form-label fw-semibold text-dark">Jumlah Keluar (Pcs) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control bg-light border-0 font-monospace" id="out_qty" name="qty" placeholder="Jumlah pcs" required min="1">
                            </div>

                            <div class="mb-4">
                                <label for="out_remarks" class="form-label fw-semibold text-dark">Alasan Pengeluaran / Keterangan <span class="text-danger">*</span></label>
                                <textarea class="form-control bg-light border-0" id="out_remarks" name="remarks" rows="4" placeholder="Contoh: Pengambilan untuk perbaikan mesin Jahit PM-LGD1-04681 oleh mekanik Budi." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-danger w-100 fw-bold shadow-sm">
                                <i class="bi bi-dash-lg me-1"></i> Simpan Stok Keluar
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: MUTASI LEDGER HISTORY -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 rounded-3 bg-white h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h5 class="fw-bold text-dark mb-0">Riwayat Mutasi & Buku Besar (Ledger History)</h5>
                <p class="text-muted small mb-0">Seluruh mutasi masuk dan keluar dari semua supplier terdokumentasi di sini.</p>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4">Tanggal / Waktu</th>
                                <th>Jenis</th>
                                <th>Qty</th>
                                <th>Supplier</th>
                                <th>Harga Beli / Unit</th>
                                <th>Catatan / Keterangan</th>
                                <th class="pe-4 text-end">Operator</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $tx)
                                <tr>
                                    <td class="ps-4 font-monospace small">
                                        {{ $tx->created_at->format('d-m-Y H:i:s') }}
                                    </td>
                                    <td>
                                        @if ($tx->type === 'IN')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill font-monospace fw-bold">
                                                <i class="bi bi-arrow-down-left me-1"></i> IN
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill font-monospace fw-bold">
                                                <i class="bi bi-arrow-up-right me-1"></i> OUT
                                            </span>
                                        @endif
                                    </td>
                                    <td class="fw-bold font-monospace fs-6">
                                        {{ $tx->qty }} pcs
                                    </td>
                                    <td class="text-secondary small">
                                        {{ $tx->supplier_name ?: '-' }}
                                    </td>
                                    <td class="font-monospace text-success fw-bold">
                                        @if ($tx->unit_price)
                                            Rp {{ number_format($tx->unit_price, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="small" style="max-width: 200px; white-space: normal; word-wrap: break-word;">
                                        {{ $tx->remarks ?: '-' }}
                                    </td>
                                    <td class="pe-4 text-end text-muted small">
                                        {{ $tx->created_by ?: 'System' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-journal-x fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                        <h6 class="fw-semibold">Belum Ada Transaksi Tercatat</h6>
                                        <p class="small text-muted mb-0">Stok masih kosong. Silakan catat transaksi masuk pertama Anda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($transactions->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Menampilkan {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }} dari {{ $transactions->total() }} riwayat</span>
                        <div>{{ $transactions->links() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
