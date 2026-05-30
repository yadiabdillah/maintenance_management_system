@extends('layouts.app')

@section('title', 'Daftar Tiket Perbaikan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0">Tiket Perbaikan</h2>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Buat Tiket
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('tickets.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="status" class="form-label small">Filter Status</label>
                <select name="status" id="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">No. Tiket</th>
                    <th>Mesin</th>
                    <th>Pelapor</th>
                    <th>Mekanik</th>
                    <th>SLA</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td class="ps-4 fw-bold text-secondary">{{ $ticket->ticket_number }}</td>
                    <td>{{ $ticket->machine->fa_desc ?? 'N/A' }} <br><small class="text-muted">{{ $ticket->machine->fa_tag_no ?? '' }}</small></td>
                    <td>{{ $ticket->user->name ?? 'System' }}</td>
                    <td>
                        @if($ticket->assignedMechanic)
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($ticket->assignedMechanic->name) }}&background=28a745&color=fff&size=24"
                                    alt="" width="24" height="24" class="rounded-circle me-1">
                                <small>{{ $ticket->assignedMechanic->name }}</small>
                            </div>
                        @else
                            <span class="text-muted small fst-italic">-</span>
                        @endif
                    </td>
                    <td>
                        @if($ticket->sla_target_hours)
                            <small>{{ $ticket->sla_target_hours }} jam</small>
                            @if($ticket->started_at && $ticket->resolved_at)
                                @php
                                    $durJam = $ticket->started_at->diffInHours($ticket->resolved_at);
                                    $slaPct = ($durJam / $ticket->sla_target_hours) * 100;
                                @endphp
                                <br>
                                @if($slaPct <= 80)
                                    <span class="badge bg-success" style="font-size: 0.65rem;">✅ On-Time</span>
                                @elseif($slaPct <= 100)
                                    <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">⚠️ Warning</span>
                                @else
                                    <span class="badge bg-danger" style="font-size: 0.65rem;">🔴 Breach</span>
                                @endif
                            @elseif($ticket->started_at && !$ticket->resolved_at)
                                @php
                                    $elapsedJam = $ticket->started_at->diffInHours(now());
                                    $slaPct = ($elapsedJam / $ticket->sla_target_hours) * 100;
                                @endphp
                                <br>
                                @if($slaPct > 100)
                                    <span class="badge bg-danger" style="font-size: 0.65rem;">🔴 Breach</span>
                                @elseif($slaPct > 80)
                                    <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">⚠️ Warning</span>
                                @else
                                    <span class="badge bg-info text-dark" style="font-size: 0.65rem;">⏳ {{ number_format($elapsedJam, 1) }}j</span>
                                @endif
                            @endif
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        @if($ticket->priority == 'low')
                            <span class="badge bg-secondary">Low</span>
                        @elseif($ticket->priority == 'medium')
                            <span class="badge bg-warning text-dark">Medium</span>
                        @else
                            <span class="badge bg-danger">High</span>
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
                    <td>{{ $ticket->created_at->format('d M Y') }}</td>
                    <td class="text-end pe-4">
                        <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">Belum ada tiket perbaikan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tickets->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $tickets->links() }}
    </div>
    @endif
</div>
@endsection
