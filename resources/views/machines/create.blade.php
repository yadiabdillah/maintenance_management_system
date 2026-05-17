@extends('layouts.app')

@section('title', 'Tambah Mesin Manual')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('machines.index') }}">Master Data Mesin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Mesin Manual</li>
            </ol>
        </nav>
        <h1 class="h2 fw-bold text-dark mb-0">Tambah Mesin Manual</h1>
        <p class="text-muted mb-0">Masukkan data mesin baru ke dalam sistem registrasi aset.</p>
    </div>
    <div>
        <a href="{{ route('machines.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-x-lg me-1"></i> Batal
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3 bg-white p-4">
    <div class="card-body">
        <form action="{{ route('machines.store') }}" method="POST">
            @csrf
            
            <!-- SECTION 1: IDENTIFIKASI ASET -->
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">
                <i class="bi bi-cpu-fill text-primary me-2"></i> Identifikasi Aset
            </h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="fa_tag_no" class="form-label fw-semibold text-dark">FA Tag No <span class="text-danger">*</span></label>
                    <input type="text" class="form-control bg-light border-0" id="fa_tag_no" name="fa_tag_no" placeholder="Contoh: PM-LGD1-120400-04681" required>
                    <div class="form-text small text-muted">Nomor Tag Aset unik terdaftar.</div>
                </div>
                <div class="col-md-4">
                    <label for="onsite" class="form-label fw-semibold text-dark">On Site</label>
                    <select class="form-select bg-light border-0" id="onsite" name="onsite">
                        <option value="" disabled selected>Pilih Site</option>
                        <option value="1MJL">1MJL</option>
                        <option value="1SKB">1SKB</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="family_desc" class="form-label fw-semibold text-dark">Family Description</label>
                    <input type="text" class="form-control bg-light border-0" id="family_desc" name="family_desc" placeholder="Contoh: Plant and Machinery">
                </div>
                <div class="col-md-6">
                    <label for="fa_desc" class="form-label fw-semibold text-dark">Nama / Model Mesin <span class="text-danger">*</span></label>
                    <input type="text" class="form-control bg-light border-0" id="fa_desc" name="fa_desc" placeholder="Contoh: NEW INDUSTRIAL 2 HEADS AUTOMATIC HEAT PRESS MACHINE" required>
                </div>
                <div class="col-md-6">
                    <label for="fa_sub_desc" class="form-label fw-semibold text-dark">Sub Deskripsi (Tipe/Detail Brand)</label>
                    <input type="text" class="form-control bg-light border-0" id="fa_sub_desc" name="fa_sub_desc" placeholder="Contoh: LABEL PICKER DEVICE OKURMA BRAND TYPE: JC-16G">
                </div>
                <div class="col-md-6">
                    <label for="serial_number" class="form-label fw-semibold text-dark">Serial Number Pabrik</label>
                    <input type="text" class="form-control bg-light border-0 font-monospace" id="serial_number" name="serial_number" placeholder="Contoh: 2026013006-315-25">
                </div>
                <div class="col-md-6">
                    <label for="family_desc_short" class="form-label fw-semibold text-dark">FA Unit</label>
                    <input type="text" class="form-control bg-light border-0" id="fa_unit" name="fa_unit" placeholder="Contoh: SET / UN">
                </div>
            </div>

            <!-- SECTION 2: LOKASI & PENUGASAN -->
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">
                <i class="bi bi-geo-alt-fill text-danger me-2"></i> Lokasi & Penugasan
            </h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="sect_code" class="form-label fw-semibold text-dark">Departemen / Seksi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control bg-light border-0" id="sect_code" name="sect_code" placeholder="Contoh: Sewing" required>
                </div>
                <div class="col-md-4">
                    <label for="loc_code" class="form-label fw-semibold text-dark">Gedung / Lokasi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control bg-light border-0" id="loc_code" name="loc_code" placeholder="Contoh: LYG-MJLK" required>
                </div>
                <div class="col-md-4">
                    <label for="line_code" class="form-label fw-semibold text-dark">Line Kerja Detail</label>
                    <input type="text" class="form-control bg-light border-0 font-monospace" id="line_code" name="line_code" placeholder="Contoh: ENG PF-01">
                </div>
                <div class="col-md-4">
                    <label for="condition_status" class="form-label fw-semibold text-dark">Status Kondisi Awal</label>
                    <select class="form-select bg-light border-0" id="condition_status" name="condition_status">
                        <option value="Good" selected>Good (Bagus)</option>
                        <option value="Needs Repair">Needs Repair (Butuh Servis)</option>
                        <option value="Repairing">Repairing (Sedang Diperbaiki)</option>
                        <option value="Broken">Broken (Rusak Total)</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label for="remark" class="form-label fw-semibold text-dark">Keterangan / Catatan Tambahan</label>
                    <input type="text" class="form-control bg-light border-0" id="remark" name="remark" placeholder="Contoh: Mesin baru di line 3">
                </div>
            </div>

            <!-- SECTION 3: FINANSIAL & PEMBELIAN -->
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">
                <i class="bi bi-wallet2 text-success me-2"></i> Finansial, Pembelian & Kepabeanan
            </h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="acq_cost" class="form-label fw-semibold text-dark">Biaya Akuisisi (Harga Rp)</label>
                    <input type="number" class="form-control bg-light border-0 text-success fw-bold" id="acq_cost" name="acq_cost" placeholder="Contoh: 60411540">
                </div>
                <div class="col-md-4">
                    <label for="acq_date" class="form-label fw-semibold text-dark">Tanggal Pembelian</label>
                    <input type="date" class="form-control bg-light border-0 font-monospace" id="acq_date" name="acq_date">
                </div>
                <div class="col-md-4">
                    <label for="local_import" class="form-label fw-semibold text-dark">Local / Import</label>
                    <select class="form-select bg-light border-0" id="local_import" name="local_import">
                        <option value="" disabled selected>Pilih Metode</option>
                        <option value="Local">Local</option>
                        <option value="Import">Import</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="supp_code" class="form-label fw-semibold text-dark">Kode Supplier</label>
                    <input type="text" class="form-control bg-light border-0" id="supp_code" name="supp_code" placeholder="Contoh: 1HON001">
                </div>
                <div class="col-md-4">
                    <label for="supp_name" class="form-label fw-semibold text-dark">Nama Supplier</label>
                    <input type="text" class="form-control bg-light border-0" id="supp_name" name="supp_name" placeholder="Contoh: Hong Lin Sewing Machine Pte Ltd">
                </div>
                <div class="col-md-4">
                    <label for="supp_invoice" class="form-label fw-semibold text-dark">Supplier Invoice No.</label>
                    <input type="text" class="form-control bg-light border-0" id="supp_invoice" name="supp_invoice" placeholder="Contoh: INV-260369">
                </div>
                <div class="col-md-4">
                    <label for="bc_doc" class="form-label fw-semibold text-dark">BC Document No.</label>
                    <input type="text" class="form-control bg-light border-0" id="bc_doc" name="bc_doc" placeholder="Contoh: 000615">
                </div>
                <div class="col-md-4">
                    <label for="ref_sage" class="form-label fw-semibold text-dark">Ref Sage</label>
                    <input type="text" class="form-control bg-light border-0" id="ref_sage" name="ref_sage" placeholder="Contoh: 1MJL-AP26-007791">
                </div>
                <div class="col-md-4">
                    <label for="sage_coa" class="form-label fw-semibold text-dark">Sage COA</label>
                    <input type="text" class="form-control bg-light border-0" id="sage_coa" name="sage_coa" placeholder="Contoh: 21291004">
                </div>
                <div class="col-md-12">
                    <label for="link_doc" class="form-label fw-semibold text-dark">Tautan Dokumen SharePoint</label>
                    <input type="url" class="form-control bg-light border-0 text-primary" id="link_doc" name="link_doc" placeholder="Contoh: https://leeyingroup.sharepoint.com/... ">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('machines.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                <button type="submit" class="btn btn-primary px-5 fw-semibold shadow-sm">
                    <i class="bi bi-save me-1"></i> Simpan Mesin
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
