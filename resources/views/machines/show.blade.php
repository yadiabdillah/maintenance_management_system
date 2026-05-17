@extends('layouts.app')

@section('title', 'Detail Mesin - ' . $machine->fa_tag_no)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('machines.index') }}">Master Data Mesin</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $machine->fa_tag_no }}</li>
            </ol>
        </nav>
        <h1 class="h2 fw-bold text-dark mb-0">{{ $machine->fa_desc }}</h1>
        <p class="text-muted mb-0">Nomor Aset: <span class="font-monospace text-secondary fw-semibold">{{ $machine->fa_tag_no }}</span></p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0 gap-2">
        <a href="{{ route('machines.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <a href="{{ route('machines.edit', $machine->id) }}" class="btn btn-warning shadow-sm">
            <i class="bi bi-pencil me-1"></i> Edit Aset
        </a>
    </div>
</div>

<div class="row">
    <!-- Left Panel: Profile & Status Card -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 rounded-3 bg-white text-center p-4 h-100">
            <div class="card-body">
                <div class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded-circle mb-3 shadow-sm" style="width: 80px; height: 80px;">
                    <i class="bi bi-cpu fs-1"></i>
                </div>
                <h4 class="fw-bold mb-1">{{ $machine->fa_tag_no }}</h4>
                <p class="text-muted small mb-3">{{ $machine->family_desc ?: 'Plant and Machinery' }}</p>

                <!-- Condition Badge -->
                <div class="mb-4">
                    @php
                        $cond = $machine->condition_status;
                        $badgeClass = 'bg-success';
                        if ($cond === 'Needs Repair') $badgeClass = 'bg-danger';
                        elseif ($cond === 'Repairing') $badgeClass = 'bg-warning text-dark';
                        elseif ($cond === 'Broken') $badgeClass = 'bg-dark';
                    @endphp
                    <span class="badge {{ $badgeClass }} px-4 py-2 rounded-pill fs-6 shadow-sm">
                        {{ $cond ?: 'Good' }}
                    </span>
                </div>

                <hr class="my-4">

                <!-- Key Quick Info -->
                <div class="text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Departemen / Seksi:</span>
                        <span class="fw-bold small">{{ $machine->sect_code ?: '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Lokasi (Gedung):</span>
                        <span class="fw-bold small">{{ $machine->loc_code ?: '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Line Kerja:</span>
                        <span class="fw-bold small font-monospace">{{ $machine->line_code ?: '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Serial Number:</span>
                        <span class="fw-bold small font-monospace">{{ $machine->serial_number ?: '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Tanggal Akuisisi:</span>
                        <span class="fw-bold small font-monospace">{{ $machine->acq_date ? date('d-m-Y', strtotime($machine->acq_date)) : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel: Full Metadata Tabs -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 rounded-3 bg-white h-100">
            <div class="card-header bg-white border-0 pt-3">
                <!-- Bootstrap Tabs Navigation -->
                <ul class="nav nav-pills card-header-pills" id="machineTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-3 py-2 fw-semibold" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                            <i class="bi bi-info-circle me-1"></i> Informasi Umum
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-3 py-2 fw-semibold" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab" aria-controls="financial" aria-selected="false">
                            <i class="bi bi-currency-dollar me-1"></i> Keuangan & Dokumen
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-3 py-2 fw-semibold" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab" aria-controls="system" aria-selected="false">
                            <i class="bi bi-shield-lock me-1"></i> Metadata & Sistem
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content" id="machineTabContent">
                    
                    <!-- TAB 1: GENERAL INFO -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Spesifikasi & Informasi Lapangan</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small d-block">Nama / Deskripsi Aset</label>
                                <span class="fw-semibold text-dark fs-6">{{ $machine->fa_desc ?: '-' }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">Sub Deskripsi (Model/Type)</label>
                                <span class="fw-semibold text-dark fs-6">{{ $machine->fa_sub_desc ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block">On Site Code</label>
                                <span class="fw-semibold text-dark">{{ $machine->onsite ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block">Family Description</label>
                                <span class="fw-semibold text-dark">{{ $machine->family_desc ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block">Unit Satuan</label>
                                <span class="fw-semibold text-dark font-monospace">{{ $machine->fa_unit ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block">Departemen Utama</label>
                                <span class="fw-semibold text-dark">{{ $machine->dept_code ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block">Seksi (Section)</label>
                                <span class="fw-semibold text-dark">{{ $machine->sect_code ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block">Sub-Seksi</label>
                                <span class="fw-semibold text-dark">{{ $machine->sub_sect_code ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block">Gedung / Lokasi</label>
                                <span class="fw-semibold text-dark">{{ $machine->loc_code ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block">Line Code / Lokasi Detail</label>
                                <span class="fw-semibold text-dark font-monospace">{{ $machine->line_code ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block">Physic Tag No</label>
                                <span class="fw-semibold text-dark font-monospace">{{ $machine->physic_tag_no ?: '-' }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">Serial Number (Pabrik)</label>
                                <span class="fw-semibold text-dark font-monospace">{{ $machine->serial_number ?: '-' }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">Cross Check Serial Number</label>
                                <span class="fw-semibold text-dark font-monospace">{{ $machine->cross_check_sn ?: '-' }}</span>
                            </div>
                            <div class="col-md-12">
                                <label class="text-muted small d-block">Catatan / Remark</label>
                                <span class="fw-semibold text-dark">{{ $machine->remark ?: '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: FINANCE & DOCS -->
                    <div class="tab-pane fade" id="financial" role="tabpanel" aria-labelledby="financial-tab">
                        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Nilai Aset & Informasi Pembelian</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small d-block">Harga Akuisisi (Acquisition Cost)</label>
                                <span class="fw-bold text-success fs-5">
                                    IDR {{ $machine->acq_cost ? number_format((double)str_replace(',', '.', $machine->acq_cost), 0, ',', '.') : '-' }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">Metode Kepemilikan</label>
                                <span class="fw-semibold text-dark">{{ $machine->local_import ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block">Kode Supplier</label>
                                <span class="fw-semibold text-dark font-monospace">{{ $machine->supp_code ?: '-' }}</span>
                            </div>
                            <div class="col-md-8">
                                <label class="text-muted small d-block">Nama Supplier</label>
                                <span class="fw-semibold text-dark">{{ $machine->supp_name ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block">Nomor Invoice Supplier</label>
                                <span class="fw-semibold text-dark font-monospace">{{ $machine->supp_invoice ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block">Ref Sage</label>
                                <span class="fw-semibold text-dark font-monospace">{{ $machine->ref_sage ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block">Sage COA</label>
                                <span class="fw-semibold text-dark font-monospace">{{ $machine->sage_coa ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block">Nomor Dokumen BC</label>
                                <span class="fw-semibold text-dark font-monospace">{{ $machine->bc_doc ?: '-' }}</span>
                            </div>
                            <div class="col-md-8">
                                <label class="text-muted small d-block">Tautan Invoice Aset (SharePoint)</label>
                                @if($machine->link_doc)
                                    <a href="{{ $machine->link_doc }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Buka Tautan Invoice
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: SYSTEM METADATA -->
                    <div class="tab-pane fade" id="system" role="tabpanel" aria-labelledby="system-tab">
                        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Integrasi & Log Audit</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small d-block">Unique Database ID (UniqID)</label>
                                <span class="fw-semibold text-dark font-monospace" style="font-size: 0.85rem;">{{ $machine->uniq_id ?: '-' }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">Status Sinkronisasi (Flag Sync)</label>
                                <span class="badge bg-light text-success border px-3 py-2 mt-1">
                                    <i class="bi bi-cloud-check-fill me-1"></i> Terhubung (Flag: {{ $machine->flag_sync ?: '0' }})
                                </span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">Dibuat Oleh</label>
                                <span class="fw-semibold text-dark">{{ $machine->create_by ?: 'System' }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">Tanggal Dibuat</label>
                                <span class="fw-semibold text-dark font-monospace">{{ $machine->create_date ?: '-' }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">Terakhir Diubah Oleh</label>
                                <span class="fw-semibold text-dark">{{ $machine->last_modify_by ?: '-' }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">Tanggal Perubahan Terakhir</label>
                                <span class="fw-semibold text-dark font-monospace">{{ $machine->last_modify_date ?: '-' }}</span>
                            </div>
                            <div class="col-md-12">
                                <label class="text-muted small d-block">Log Pembaruan Terakhir (Last Update Log)</label>
                                <div class="bg-light p-2 rounded border font-monospace small mt-1 text-secondary" style="max-height: 100px; overflow-y: auto;">
                                    {{ $machine->last_update_log ?: 'Tidak ada log aktivitas.' }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
