@extends('layouts.app')

@section('title', 'Detail Tiket Perbaikan')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="{{ route('tickets.index') }}" class="text-decoration-none text-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Tiket
    </a>

    <div>
        @php
            $isAdmin = in_array(Auth::user()->role, ['Super Admin', 'Supervisor']);
            $isMyTicket = Auth::user()->role === 'Operator' && $ticket->assigned_to === Auth::id();
        @endphp

        @if($isAdmin && in_array($ticket->status, ['open', 'in_progress']))
            <a href="{{ route('tickets.assign.form', $ticket->id) }}" class="btn btn-primary me-2">
                <i class="bi bi-person-plus me-1"></i> Assign Mekanik
            </a>
        @endif
        @if($isAdmin)
            <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil-square me-1"></i> Update Status
            </a>
        @endif
        @if($isMyTicket && $ticket->status === 'in_progress')
            <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-success">
                <i class="bi bi-check2-all me-1"></i> Selesaikan Tiket
            </a>
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark">Informasi Tiket: {{ $ticket->ticket_number }}</h5>
                <div>
                    @if($ticket->status == 'open')
                        <span class="badge bg-primary fs-6">Open</span>
                    @elseif($ticket->status == 'in_progress')
                        <span class="badge bg-info text-dark fs-6">In Progress</span>
                    @elseif($ticket->status == 'resolved')
                        <span class="badge bg-success fs-6">Resolved</span>
                    @else
                        <span class="badge bg-secondary fs-6">Closed</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted mb-1">Deskripsi Kerusakan</h6>
                    <p class="fs-5">{{ $ticket->issue_description }}</p>
                </div>

                @if($ticket->photo_path)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Foto Lampiran</h6>
                    <img src="{{ asset('storage/' . $ticket->photo_path) }}" alt="Foto Kerusakan" class="img-fluid rounded border" style="max-height: 400px; object-fit: contain;">
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Kartu Detail Mesin & Laporan -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 text-dark">Detail Mesin & Laporan</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Mesin / Aset</small>
                    <strong>{{ $ticket->machine->fa_desc ?? 'N/A' }}</strong>
                    <div class="text-secondary small">{{ $ticket->machine->fa_tag_no ?? '-' }}</div>
                </div>
                <hr>
                <div class="mb-3">
                    <small class="text-muted d-block">Pelapor</small>
                    <strong>{{ $ticket->user->name ?? 'System' }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Waktu Dilaporkan</small>
                    <strong>{{ $ticket->created_at->format('d F Y, H:i') }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Prioritas</small>
                    @if($ticket->priority == 'low')
                        <span class="badge bg-secondary">Low</span>
                    @elseif($ticket->priority == 'medium')
                        <span class="badge bg-warning text-dark">Medium</span>
                    @else
                        <span class="badge bg-danger">High</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kartu Sparepart Digunakan -->
        @if($ticket->spareparts->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 text-dark">
                    <i class="bi bi-box-seam me-1 text-warning"></i> Sparepart Digunakan
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Sparepart</th>
                                <th>SKU</th>
                                <th class="text-center">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ticket->spareparts as $sparepart)
                            <tr>
                                <td class="ps-3">{{ $sparepart->name }}</td>
                                <td><code>{{ $sparepart->sku }}</code></td>
                                <td class="text-center">
                                    <span class="badge bg-warning text-dark">{{ $sparepart->pivot->qty }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Kartu Assignment Mekanik & SLA -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 text-dark">Mekanik & SLA</h6>
            </div>
            <div class="card-body">
                @if($ticket->assignedMechanic)
                <div class="mb-3">
                    <small class="text-muted d-block">Mekanik Ditugaskan</small>
                    <div class="d-flex align-items-center mt-1">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($ticket->assignedMechanic->name) }}&background=28a745&color=fff"
                            alt="" width="32" height="32" class="rounded-circle me-2">
                        <strong>{{ $ticket->assignedMechanic->name }}</strong>
                    </div>
                </div>
                <hr>
                @else
                <div class="mb-3">
                    <small class="text-muted d-block">Mekanik Ditugaskan</small>
                    <span class="text-muted fst-italic">Belum di-assign</span>
                </div>
                <hr>
                @endif

                <div class="mb-3">
                    <small class="text-muted d-block">Target SLA</small>
                    <strong>{{ $ticket->sla_target_hours ?? '-' }} Jam</strong>
                </div>

                @if($ticket->started_at)
                <div class="mb-3">
                    <small class="text-muted d-block">Mulai Pengerjaan</small>
                    <strong>{{ $ticket->started_at->format('d F Y, H:i') }}</strong>
                </div>
                @endif

                @if($ticket->resolved_at)
                <div class="mb-3">
                    <small class="text-muted d-block">Selesai Pengerjaan</small>
                    <strong>{{ $ticket->resolved_at->format('d F Y, H:i') }}</strong>
                </div>
                @endif

                @if($ticket->started_at && $ticket->resolved_at && $ticket->sla_target_hours)
                    @php
                        $durationHours = $ticket->started_at->diffInHours($ticket->resolved_at);
                        $durationMinutes = $ticket->started_at->diffInMinutes($ticket->resolved_at) % 60;
                        $slaPercentage = ($durationHours / $ticket->sla_target_hours) * 100;
                    @endphp
                    <hr>
                    <div class="mb-2">
                        <small class="text-muted d-block">Durasi Aktual</small>
                        <strong>{{ floor($durationHours) }}j {{ $durationMinutes }}m</strong>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block">Status SLA</small>
                        @if($slaPercentage <= 80)
                            <span class="badge bg-success fs-6 mt-1">✅ On-Time</span>
                        @elseif($slaPercentage <= 100)
                            <span class="badge bg-warning text-dark fs-6 mt-1">⚠️ Warning</span>
                        @else
                            <span class="badge bg-danger fs-6 mt-1">🔴 Breach</span>
                        @endif
                        <small class="text-muted d-block mt-1">{{ number_format($slaPercentage, 1) }}% dari target {{ $ticket->sla_target_hours }} jam</small>
                    </div>
                @endif

                @if($ticket->assigned_to && !$ticket->started_at && in_array(Auth::user()->role, ['Super Admin', 'Supervisor']))
                <hr>
                <div class="d-grid">
                    <a href="{{ route('tickets.assign.form', $ticket->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-arrow-repeat me-1"></i> Ganti Mekanik
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
