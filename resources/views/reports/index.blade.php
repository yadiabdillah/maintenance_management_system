@extends('layouts.app')

@section('title', 'Laporan Tiket - MMS')

@push('styles')
<style>
    .filter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    .filter-card .form-control::placeholder {
        color: rgba(255,255,255,0.5);
    }
    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        background: rgba(255,255,255,0.25);
        border-color: rgba(255,255,255,0.6);
        color: #fff;
        box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.15);
    }
    .filter-card .form-select option {
        background: #667eea;
        color: #fff;
    }
    .stat-card {
        transition: transform 0.15s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i> Laporan Tiket
    </h1>
</div>

<!-- Filter Card -->
<div class="card filter-card shadow-sm mb-4 border-0">
    <div class="card-body">
        <form action="{{ route('reports.index') }}" method="GET" id="filterForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label"><i class="bi bi-calendar me-1"></i>Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                        value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label"><i class="bi bi-calendar me-1"></i>Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                        value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="technician" class="form-label"><i class="bi bi-person-gear me-1"></i>Teknisi</label>
                    <select name="technician" id="technician" class="form-select">
                        <option value="all" {{ request('technician', 'all') == 'all' ? 'selected' : '' }}>Semua Teknisi</option>
                        @foreach($mechanics as $mechanic)
                            <option value="{{ $mechanic->id }}" {{ request('technician') == $mechanic->id ? 'selected' : '' }}>
                                {{ $mechanic->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label"><i class="bi bi-tag me-1"></i>Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-light w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col">
        <div class="card stat-card border-0 shadow-sm bg-primary text-white">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">{{ $totalTickets }}</h3>
                <small>Total Tiket</small>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card stat-card border-0 shadow-sm bg-danger text-white">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">{{ $totalOpen }}</h3>
                <small>Open</small>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card stat-card border-0 shadow-sm bg-warning text-white">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">{{ $totalInProgress }}</h3>
                <small>In Progress</small>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card stat-card border-0 shadow-sm bg-success text-white">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">{{ $totalResolved }}</h3>
                <small>Resolved</small>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card stat-card border-0 shadow-sm bg-secondary text-white">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">{{ $totalClosed }}</h3>
                <small>Closed</small>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted">
        @if(request()->hasAny(['start_date', 'end_date', 'technician', 'status']))
            Menampilkan <strong>{{ $tickets->count() }}</strong> tiket
            @if(request('start_date') && request('end_date'))
                dari <strong>{{ request('start_date') }}</strong> sampai <strong>{{ request('end_date') }}</strong>
            @endif
        @else
            <i class="bi bi-info-circle me-1"></i> Gunakan filter di atas, lalu klik tombol <i class="bi bi-search"></i>
        @endif
    </span>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.export.csv', request()->query()) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Download CSV
        </a>
        <a href="{{ route('reports.export.pdf', request()->query()) }}" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
        </a>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">No. Tiket</th>
                        <th>Mesin</th>
                        <th>Pelapor</th>
                        <th>Mekanik</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>SLA</th>
                        <th class="text-nowrap">Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td class="ps-3">
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="fw-medium text-decoration-none">
                                {{ $ticket->ticket_number }}
                            </a>
                        </td>
                        <td>
                            <span class="d-block">{{ $ticket->machine?->fa_desc ?? 'N/A' }}</span>
                            <small class="text-muted">{{ $ticket->machine?->fa_tag_no ?? '' }}</small>
                        </td>
                        <td>{{ $ticket->user?->name ?? 'System' }}</td>
                        <td>{{ $ticket->assignedMechanic?->name ?? '-' }}</td>
                        <td>
                            @if($ticket->priority == 'high')
                                <span class="badge bg-danger">High</span>
                            @elseif($ticket->priority == 'medium')
                                <span class="badge bg-warning text-dark">Medium</span>
                            @else
                                <span class="badge bg-secondary">Low</span>
                            @endif
                        </td>
                        <td>
                            @if($ticket->status == 'open')
                                <span class="badge bg-primary">Open</span>
                            @elseif($ticket->status == 'in_progress')
                                <span class="badge bg-info text-dark">In Progress</span>
                            @elseif($ticket->status == 'resolved')
                                <span class="badge bg-success">Resolved</span>
                            @else
                                <span class="badge bg-secondary">Closed</span>
                            @endif
                        </td>
                        <td>
                            @if($ticket->started_at && $ticket->resolved_at && $ticket->sla_target_hours)
                                @php
                                    $durationHours = $ticket->started_at->diffInHours($ticket->resolved_at);
                                    $slaPercentage = $ticket->sla_target_hours > 0 ? ($durationHours / $ticket->sla_target_hours) * 100 : 0;
                                @endphp
                                @if($slaPercentage <= 80)
                                    <span class="badge bg-success">On-Time</span>
                                @elseif($slaPercentage <= 100)
                                    <span class="badge bg-warning text-dark">Warning</span>
                                @else
                                    <span class="badge bg-danger">Breach</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-nowrap">{{ $ticket->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Tidak ada data tiket</p>
                            <small>Gunakan filter untuk menampilkan data</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">Menampilkan {{ $tickets->firstItem() }} - {{ $tickets->lastItem() }} dari {{ $tickets->total() }} tiket</span>
                <div>{{ $tickets->links() }}</div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
