@extends('layouts.app')

@section('title', 'Tambah Master Sparepart')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('spareparts.index') }}">Inventori Sparepart</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Master</li>
            </ol>
        </nav>
        <h1 class="h2 fw-bold text-dark mb-0">Tambah Master Sparepart</h1>
        <p class="text-muted mb-0">Daftarkan jenis suku cadang baru ke dalam katalog master sistem.</p>
    </div>
    <div>
        <a href="{{ route('spareparts.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-x-lg me-1"></i> Batal
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3 bg-white p-4" style="max-width: 600px;">
    <div class="card-body">
        <form action="{{ route('spareparts.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="sku" class="form-label fw-semibold text-dark">SKU / Kode Part <span class="text-danger">*</span></label>
                <input type="text" class="form-control bg-light border-0 font-monospace" id="sku" name="sku" placeholder="Contoh: SP-JK-DBX1-002" required value="{{ old('sku') }}">
                <div class="form-text small text-muted">Kode identifikasi suku cadang yang unik (Barcode/SKU).</div>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold text-dark">Nama Barang / Deskripsi Sparepart <span class="text-danger">*</span></label>
                <input type="text" class="form-control bg-light border-0" id="name" name="name" placeholder="Contoh: Jarum Mesin Sewing Juki DBx1 Size 14" required value="{{ old('name') }}">
            </div>

            <div class="mb-4">
                <label for="min_stock" class="form-label fw-semibold text-dark">Batas Minimum Stok (Alert) <span class="text-danger">*</span></label>
                <input type="number" class="form-control bg-light border-0 font-monospace" id="min_stock" name="min_stock" placeholder="Contoh: 10" required value="{{ old('min_stock', 5) }}">
                <div class="form-text small text-muted">Sistem akan memberi tanda peringatan merah jika stok turun mencapai batas ini.</div>
            </div>

            <div class="alert alert-info border-0 rounded-3 small mb-4">
                <i class="bi bi-info-circle-fill me-2"></i> <strong>Catatan Stok Awal:</strong> Stok awal pendaftaran baru adalah <code>0 pcs</code>. Gunakan fitur **Transaksi Masuk (IN)** pada halaman Kartu Stok sparepart untuk menambah persediaan pertama kali beserta info supplier & harganya.
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                    <i class="bi bi-save me-1"></i> Daftarkan Sparepart
                </button>
                <a href="{{ route('spareparts.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
