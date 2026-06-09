@extends('layouts.app')

@section('title', 'Laporan Data Mesin - MMS')

@push('styles')
<style>
    .filter-card {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .filter-card label {
        color: rgba(255,255,255,0.85);
        font-size: 0.85rem;
        font-weight: 500;
    }
    .filter-card .form-control,
    .filter-card .form-select {
        border: 1px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.15);
        color: #fff;
    }
    .filter-card .form-control::placeholder { color: rgba(255,255,255,0.5); }
    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        background: rgba(255,255,255,0.25);
        border-color: rgba(255,255,255,0.6);
        color: #fff;
        box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.15);
    }
    .filter-card .form-select option { background: #f5576c; color: #fff; }
    .stat-card { transition: transform 0.15s; }
    .stat-card:hover { transform: translateY(-2px); }
    .condition-good { background-color: #f0fff4; }
    .condition-needs-repair { background-color: #fff8e1; }
    .condition-repairing { background-color: #e3f2fd; }
    .condition-broken { background-color: #fff3f3; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-cpu me-2 text-danger"></i> Laporan Data Mesin
    </h1>
</div>

<!-- Filter Card -->
<div class="card filter-card shadow-sm mb-4 border-0">
    <div class="card-body">
        <form action="{{ route('reports.machine.data') }}" method="GET" id="filterForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label"><i class="bi bi-search me-1"></i>Cari</label>
                    <input type="text" name="search" id="search" class="form-control"
                        placeholder="FA Tag No / Deskripsi / Serial Number..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label for="condition" class="form-label"><i class="bi bi-heart-pulse me-1"></i>Kondisi</label>
                    <select name="condition" id="condition" class="form-select">
                        <option value="all" {{ request('condition', 'all') == 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="Good" {{ request('condition') == 'Good' ? 'selected' : '' }}>Good</option>
                        <option value="Needs Repair" {{ request('condition') == 'Needs Repair' ? 'selected' : '' }}>Needs Repair</option>
                        <option value="Repairing" {{ request('condition') == 'Repairing' ? 'selected' : '' }}>Repairing</option>
                        <option value="Broken" {{ request('condition') == 'Broken' ? 'selected' : '' }}>Broken</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="section" class="form-label"><i class="bi bi-diagram-3 me-1"></i>Section</label>
                    <select name="section" id="section" class="form-select">
                        <option value="all" {{ request('section', 'all') == 'all' ? 'selected' : '' }}>Semua</option>
                        @foreach($sections as $section)
                            <option value="{{ $section }}" {{ request('section') == $section ? 'selected' : '' }}>
                                {{ $section }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="location" class="form-label"><i class="bi bi-geo-alt me-1"></i>Lokasi</label>
                    <select name="location" id="location" class="form-select">
                        <option value="all" {{ request('location', 'all') == 'all' ? 'selected' : '' }}>Semua</option>
                        @foreach($locations as $location)
                            <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                {{ $location }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-light w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('reports.machine.data') }}" class="btn btn-outline-light w-100">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card stat-card border-0 shadow-sm bg-primary text-white">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">{{ $totalMachines }}</h3>
                <small>Total Mesin</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stat-card border-0 shadow-sm bg-success text-white">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">{{ $goodCondition }}</h3>
                <small>Good</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stat-card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">{{ $needsRepair }}</h3>
                <small>Needs Repair</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stat-card border-0 shadow-sm bg-info text-white">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">{{ $repairing }}</h3>
                <small>Repairing</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stat-card border-0 shadow-sm bg-danger text-white">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">{{ $broken }}</h3>
                <small>Broken</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stat-card border-0 shadow-sm bg-secondary text-white">
            <div class="card-body text-center py-3">
                <h6 class="mb-0">Rp {{ number_format($totalAcqCost, 0, ',', '.') }}</h6>
                <small>Total Nilai</small>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted">
        @if(request()->hasAny(['search', 'condition', 'section', 'location']))
            Menampilkan <strong>{{ $machines->count() }}</strong> mesin
            @if(request('search'))
                | Pencarian: "<strong>{{ request('search') }}</strong>"
            @endif
        @else
            <i class="bi bi-info-circle me-1"></i> Menampilkan semua data mesin ({{ $totalMachines }})
        @endif
    </span>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.machine.data.csv', request()->query()) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Download CSV
        </a>
        <a href="{{ route('reports.machine.data.pdf', request()->query()) }}" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
        </a>
    </div>
</div>

<!-- Main Table: Data Mesin -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-list-check me-2 text-danger"></i>Daftar Data Mesin</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">No</th>
                        <th>FA Tag No</th>
                        <th>Deskripsi Mesin</th>
                        <th>Sub Deskripsi</th>
                        <th>Serial Number</th>
                        <th>Section</th>
                        <th>Lokasi</th>
                        <th>Line</th>
                        <th>Kondisi</th>
                        <th>Supplier</th>
                        <th class="text-end">Nilai Perolehan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($machines as $index => $machine)
                    @php
                        $conditionClass = match($machine->condition_status) {
                            'Good' => 'condition-good',
                            'Needs Repair' => 'condition-needs-repair',
                            'Repairing' => 'condition-repairing',
                            'Broken' => 'condition-broken',
                            default => '',
                        };
                    @endphp
                    <tr class="{{ $conditionClass }}">
                        <td class="ps-3">{{ $index + 1 }}</td>
                        <td><code>{{ $machine->fa_tag_no }}</code></td>
                        <td>
                            <span class="fw-medium">{{ $machine->fa_desc }}</span>
                        </td>
                        <td><small class="text-muted">{{ $machine->fa_sub_desc }}</small></td>
                        <td>{{ $machine->serial_number }}</td>
                        <td>{{ $machine->sect_code }}</td>
                        <td>{{ $machine->loc_code }}</td>
                        <td>{{ $machine->line_code }}</td>
                        <td>
                            @if($machine->condition_status == 'Good')
                                <span class="badge bg-success">Good</span>
                            @elseif($machine->condition_status == 'Needs Repair')
                                <span class="badge bg-warning text-dark">Needs Repair</span>
                            @elseif($machine->condition_status == 'Repairing')
                                <span class="badge bg-info text-dark">Repairing</span>
                            @elseif($machine->condition_status == 'Broken')
                                <span class="badge bg-danger">Broken</span>
                            @else
                                <span class="badge bg-secondary">{{ $machine->condition_status ?? '-' }}</span>
                            @endif
                        </td>
                        <td>{{ $machine->supp_name }}</td>
                        <td class="text-end">
                            @if($machine->acq_cost)
                                Rp {{ number_format((float) str_replace(['.', ','], ['', '.'], $machine->acq_cost), 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Tidak ada data mesin</p>
                            <small>Sesuaikan filter atau impor data mesin terlebih dahulu</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
</div>
@endsection