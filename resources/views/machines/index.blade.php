@extends('layouts.app')

@section('title', 'Master Data Mesin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">Master Data Mesin</h1>
        <p class="text-muted mb-0">Kelola dan sinkronisasi seluruh aset mesin pabrik garment secara terintegrasi.</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0 gap-2">
        <!-- Button Trigger Import Modal -->
        <button type="button" class="btn btn-outline-success shadow-sm" data-bs-toggle="modal" data-bs-target="#importTsvModal">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i> Sync / Import Excel
        </button>
        <a href="{{ route('machines.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Mesin Manual
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filter & Search Panel -->
<div class="card shadow-sm border-0 mb-4 bg-white rounded-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('machines.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari FA Tag / Model / S/N..." value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="col-md-2">
                <select name="sect_code" class="form-select bg-light border-0" onchange="this.form.submit()">
                    <option value="">Semua Departemen</option>
                    @foreach($sections as $sect)
                        <option value="{{ $sect }}" {{ request('sect_code') == $sect ? 'selected' : '' }}>{{ $sect }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="loc_code" class="form-select bg-light border-0" onchange="this.form.submit()">
                    <option value="">Semua Lokasi</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}" {{ request('loc_code') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="condition_status" class="form-select bg-light border-0" onchange="this.form.submit()">
                    <option value="">Semua Kondisi</option>
                    <option value="Good" {{ request('condition_status') == 'Good' ? 'selected' : '' }}>Good (Bagus)</option>
                    <option value="Needs Repair" {{ request('condition_status') == 'Needs Repair' ? 'selected' : '' }}>Needs Repair (Butuh Servis)</option>
                    <option value="Repairing" {{ request('condition_status') == 'Repairing' ? 'selected' : '' }}>Repairing (Sedang Diperbaiki)</option>
                    <option value="Broken" {{ request('condition_status') == 'Broken' ? 'selected' : '' }}>Broken (Rusak Total)</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                @if(request()->anyFilled(['search', 'sect_code', 'loc_code', 'condition_status']))
                    <a href="{{ route('machines.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Machine Data Table -->
<div class="card shadow-sm border-0 rounded-3 overflow-hidden bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" class="ps-4">No</th>
                        <th scope="col">FA Tag No</th>
                        <th scope="col">Nama / Model Mesin</th>
                        <th scope="col">Dept / Seksi</th>
                        <th scope="col">Lokasi & Line</th>
                        <th scope="col">Serial Number</th>
                        <th scope="col">Status Kondisi</th>
                        <th scope="col" class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($machines as $index => $machine)
                        <tr>
                            <td class="ps-4 text-muted">{{ $machines->firstItem() + $index }}</td>
                            <td>
                                <span class="badge bg-secondary font-monospace p-2" style="font-size: 0.85rem;">
                                    {{ $machine->fa_tag_no }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $machine->fa_desc }}</div>
                                <div class="small text-muted">{{ $machine->fa_sub_desc ?: '-' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border p-2">
                                    <i class="bi bi-cpu-fill text-primary me-1"></i> {{ $machine->sect_code ?: 'General' }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $machine->loc_code ?: '-' }}</div>
                                <div class="small text-muted font-monospace">{{ $machine->line_code ?: '-' }}</div>
                            </td>
                            <td class="font-monospace text-secondary">
                                {{ $machine->serial_number ?: '-' }}
                            </td>
                            <td>
                                @php
                                    $cond = $machine->condition_status;
                                    $badgeClass = 'bg-success';
                                    if ($cond === 'Needs Repair') $badgeClass = 'bg-danger';
                                    elseif ($cond === 'Repairing') $badgeClass = 'bg-warning text-dark';
                                    elseif ($cond === 'Broken') $badgeClass = 'bg-dark';
                                @endphp
                                <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill">
                                    {{ $cond ?: 'Good' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm" role="group">
                                    <a href="{{ route('machines.show', $machine->id) }}" class="btn btn-sm btn-light border" title="Detail">
                                        <i class="bi bi-eye text-primary"></i>
                                    </a>
                                    <a href="{{ route('machines.edit', $machine->id) }}" class="btn btn-sm btn-light border" title="Edit">
                                        <i class="bi bi-pencil text-warning"></i>
                                    </a>
                                    <form action="{{ route('machines.destroy', $machine->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data mesin ini?');" class="d-inline">
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
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-cpu fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                <h5 class="fw-semibold">Tidak Ada Data Mesin</h5>
                                <p class="small text-muted">Mulai dengan menambah mesin manual atau gunakan fitur Sync / Import Excel.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($machines->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">Menampilkan {{ $machines->firstItem() }} - {{ $machines->lastItem() }} dari {{ $machines->total() }} mesin</span>
                <div>{{ $machines->links() }}</div>
            </div>
        </div>
    @endif
</div>

<!-- Excel / TSV Import Modal -->
<div class="modal fade" id="importTsvModal" tabindex="-1" aria-labelledby="importTsvModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white border-0 py-3">
                <h5 class="modal-title fw-bold" id="importTsvModalLabel">
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i> Sync / Import Data dari Excel
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('machines.import') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 mb-3">
                        <h6 class="fw-bold mb-1"><i class="bi bi-info-circle-fill me-2"></i> Cara Mengimpor Data Excel:</h6>
                        <ol class="small mb-0 ps-3">
                            <li>Buka spreadsheet Excel / SharePoint / Sage register aset Anda.</li>
                            <li>Pilih seluruh tabel data termasuk baris header (misal: <code>FATagNo</code>, <code>FADesc</code>, dll) lalu klik <strong>Copy (Ctrl+C)</strong>.</li>
                            <li>Tempelkan hasil copy tersebut langsung pada area teks di bawah ini.</li>
                            <li>Klik tombol <strong>"Mulai Sinkronisasi"</strong>. Sistem akan mencocokkan, membuat data baru, atau memperbarui data lama secara otomatis (Upsert).</li>
                        </ol>
                    </div>

                    <div class="mb-3">
                        <label for="tsv_data" class="form-label fw-semibold text-dark">Tempelkan Data Excel Disini (TSV):</label>
                        <textarea class="form-control font-monospace bg-light" id="tsv_data" name="tsv_data" rows="12" placeholder="FATagNo&#9;FADesc&#9;SectCode&#9;LocCode&#9;SerialNumber&#9;ConditionStatus&#10;PM-LGD1-04681&#9;AUTOMATIC PRESS MACHINE&#9;Sewing&#9;LYG-MJLK&#9;SN-00295&#9;Good" required style="font-size: 0.8rem;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light py-3 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 fw-semibold shadow-sm">
                        <i class="bi bi-arrow-repeat me-1 animate-spin"></i> Mulai Sinkronisasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
