@extends('layouts.app')

@section('title', 'Dashboard - MMS')

@push('styles')
<style>
    .card-hover {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        cursor: pointer;
    }
    .card-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        @if($isMechanic)
            <i class="bi bi-wrench-adjustable me-2 text-success"></i> Dashboard Mekanik
        @elseif($isSupervisorOrAdmin)
            <i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard {{ Auth::user()->role }}
        @else
            Dashboard
        @endif
    </h1>
    <div>
        <span class="text-muted">
            <i class="bi bi-person me-1"></i> {{ Auth::user()->name }}
            <span class="badge bg-secondary ms-1">{{ Auth::user()->role }}</span>
        </span>
    </div>
</div>

<!-- Summary Cards (Clickable) -->
<div class="row mb-4">
    <div class="col-md-3">
        <a href="{{ route('tickets.index') }}" class="text-decoration-none">
            <div class="card text-white bg-primary mb-3 shadow-sm card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase mb-1">
                                @if($isMechanic) Tugas Saya Hari Ini @else Total Job Hari Ini @endif
                            </h6>
                            <h2 class="mb-0">{{ $totalToday }}</h2>
                        </div>
                        <i class="bi bi-clipboard-data fs-1"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('tickets.index', ['status' => 'open']) }}" class="text-decoration-none">
            <div class="card text-white bg-danger mb-3 shadow-sm card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase mb-1">
                                @if($isMechanic) Tugas Menunggu @else Tiket OPEN @endif
                            </h6>
                            <h2 class="mb-0">{{ $totalOpen }}</h2>
                        </div>
                        <i class="bi bi-exclamation-circle fs-1"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('tickets.index', ['status' => 'in_progress']) }}" class="text-decoration-none">
            <div class="card text-white bg-warning mb-3 shadow-sm card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase mb-1">
                                @if($isMechanic) Sedang Dikerjakan @else IN PROGRESS @endif
                            </h6>
                            <h2 class="mb-0">{{ $totalInProgress }}</h2>
                        </div>
                        <i class="bi bi-gear-wide-connected fs-1"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('tickets.index', ['status' => 'closed']) }}" class="text-decoration-none">
            <div class="card text-white bg-success mb-3 shadow-sm card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase mb-1">SLA On-Time</h6>
                            <h2 class="mb-0">{{ $slaPercentage }}%</h2>
                            <small class="opacity-75">{{ $slaOnTimeCount }} / {{ $totalResolved }} tiket tepat waktu</small>
                        </div>
                        <i class="bi bi-check2-circle fs-1"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Main Content Row -->
<div class="row">
    <!-- Chart Area -->
    <div class="col-md-8">
        <!-- Chart Aktivitas -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2 text-muted"></i>
                    @if($isMechanic) Aktivitas Saya @else Aktivitas Maintenance @endif
                    (7 Hari Terakhir)
                </h5>
            </div>
            <div class="card-body">
                @if($ticketsLast7Days->sum('open') + $ticketsLast7Days->sum('in_progress') + $ticketsLast7Days->sum('resolved') > 0)
                <div style="height: 250px;">
                    <canvas id="activityChart"></canvas>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-bar-chart" style="font-size: 4rem;"></i>
                    <p class="mt-3">Belum ada data aktivitas 7 hari terakhir.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Tabel Tugas Aktif (khusus mekanik) -->
        @if($isMechanic && count($myTickets) > 0)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-task me-2 text-success"></i> Tugas Aktif Saya
                </h5>
                <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="list-group list-group-flush">
                @foreach($myTickets as $ticket)
                <a href="{{ route('tickets.show', $ticket->id) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div>
                            <strong>{{ $ticket->machine->fa_desc ?? 'N/A' }}</strong>
                            <small class="text-muted d-block">{{ $ticket->machine->fa_tag_no ?? '' }} - {{ Str::limit($ticket->issue_description, 50) }}</small>
                        </div>
                        <div class="text-end">
                            @if($ticket->status == 'open')
                                <span class="badge bg-primary">OPEN</span>
                            @else
                                <span class="badge bg-info text-dark">IN PROGRESS</span>
                            @endif
                            <br>
                            <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Tiket Belum di-Assign (khusus supervisor/admin) -->
        @if($isSupervisorOrAdmin && count($unassignedTickets) > 0)
        <div class="card shadow-sm mb-4 border-start border-danger border-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i> Tiket Belum di-Assign
                    <span class="badge bg-danger ms-2">{{ count($unassignedTickets) }}</span>
                </h5>
                <a href="{{ route('tickets.index', ['status' => 'open']) }}" class="btn btn-sm btn-outline-danger">Lihat Semua</a>
            </div>
            <div class="list-group list-group-flush">
                @foreach($unassignedTickets as $ticket)
                <a href="{{ route('tickets.show', $ticket->id) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div>
                            <strong>{{ $ticket->machine->fa_desc ?? 'N/A' }}</strong>
                            <small class="text-muted d-block">{{ $ticket->machine->fa_tag_no ?? '' }}</small>
                            <small class="text-muted">{{ Str::limit($ticket->issue_description, 60) }}</small>
                        </div>
                        <div class="text-end">
                            @if($ticket->priority == 'high')
                                <span class="badge bg-danger">HIGH</span>
                            @elseif($ticket->priority == 'medium')
                                <span class="badge bg-warning text-dark">MEDIUM</span>
                            @else
                                <span class="badge bg-secondary">LOW</span>
                            @endif
                            <br>
                            <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="card-footer bg-white text-center py-2">
                <a href="{{ route('tickets.index', ['status' => 'open']) }}" class="text-decoration-none small">
                    <i class="bi bi-person-plus me-1"></i> Assign mekanik sekarang
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Side Panel -->
    <div class="col-md-4">
        <!-- Tiket Terbaru -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2 text-muted"></i> Tiket Terbaru
                </h5>
                <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
            </div>
            <div class="list-group list-group-flush">
                @forelse($latestTickets as $ticket)
                <a href="{{ route('tickets.show', $ticket->id) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <div>
                            <h6 class="mb-1" style="font-size: 0.85rem;">{{ $ticket->machine->fa_desc ?? 'N/A' }}</h6>
                            <small class="text-muted d-block">{{ Str::limit($ticket->issue_description, 40) }}</small>
                        </div>
                        <div class="text-end" style="min-width: 70px;">
                            @if($ticket->status == 'open')
                                <span class="badge bg-primary" style="font-size: 0.65rem;">OPEN</span>
                            @elseif($ticket->status == 'in_progress')
                                <span class="badge bg-info text-dark" style="font-size: 0.65rem;">IN PROGRESS</span>
                            @elseif($ticket->status == 'resolved')
                                <span class="badge bg-success" style="font-size: 0.65rem;">RESOLVED</span>
                            @else
                                <span class="badge bg-secondary" style="font-size: 0.65rem;">CLOSED</span>
                            @endif
                            <br>
                            @if($ticket->assignedMechanic)
                                <small class="text-muted" style="font-size: 0.65rem;">{{ $ticket->assignedMechanic->name }}</small>
                            @endif
                        </div>
                    </div>
                    <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                </a>
                @empty
                <div class="list-group-item text-center py-4 text-muted">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2 small mb-0">Belum ada tiket</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Info Cepat - Role Mekanik -->
        @if($isMechanic && count($myTickets) > 0)
        <div class="card shadow-sm mb-4 border-success">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="bi bi-check2-circle me-2"></i>Ringkasan Tugas Saya</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Tugas Aktif</span>
                    <strong>{{ count($myTickets) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Menunggu (OPEN)</span>
                    <span class="badge bg-primary">{{ $totalOpen }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Sedang Dikerjakan</span>
                    <span class="badge bg-info text-dark">{{ $totalInProgress }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Info Cepat - Role Supervisor/Admin -->
        @if($isSupervisorOrAdmin)
        <div class="card shadow-sm mb-4 border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Info Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Tiket Perlu Assign</span>
                    <strong>{{ count($unassignedTickets) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Tiket Hari Ini</span>
                    <strong>{{ $totalToday }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>SLA On-Time</span>
                    <span class="badge bg-success">{{ $slaPercentage }}%</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($ticketsLast7Days->sum('open') + $ticketsLast7Days->sum('in_progress') + $ticketsLast7Days->sum('resolved') > 0)
        const ctx = document.getElementById('activityChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($ticketsLast7Days as $day)
                        '{{ $day["label"] }}',
                    @endforeach
                ],
                datasets: [
                    {
                        label: 'Open',
                        data: [
                            @foreach($ticketsLast7Days as $day)
                                {{ $day['open'] }},
                            @endforeach
                        ],
                        backgroundColor: '#0d6efd',
                        borderRadius: 3,
                    },
                    {
                        label: 'In Progress',
                        data: [
                            @foreach($ticketsLast7Days as $day)
                                {{ $day['in_progress'] }},
                            @endforeach
                        ],
                        backgroundColor: '#ffc107',
                        borderRadius: 3,
                    },
                    {
                        label: 'Resolved',
                        data: [
                            @foreach($ticketsLast7Days as $day)
                                {{ $day['resolved'] }},
                            @endforeach
                        ],
                        backgroundColor: '#198754',
                        borderRadius: 3,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            padding: 10,
                            font: { size: 11 }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
        @endif
    });
</script>
@endpush
