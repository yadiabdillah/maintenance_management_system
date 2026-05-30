<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tiket Maintenance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #0d6efd;
        }
        .header h1 {
            font-size: 18px;
            color: #0d6efd;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            gap: 8px;
        }
        .summary-item {
            flex: 1;
            text-align: center;
            padding: 8px;
            border-radius: 4px;
            color: #fff;
            font-size: 11px;
        }
        .summary-item .number {
            font-size: 20px;
            font-weight: bold;
            display: block;
        }
        .bg-primary { background: #0d6efd; }
        .bg-success { background: #198754; }
        .bg-danger { background: #dc3545; }
        .bg-warning { background: #ffc107; color: #333; }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        th {
            background: #0d6efd;
            color: #fff;
            padding: 6px 4px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 5px 4px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-danger { background: #dc3545; color: #fff; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-secondary { background: #6c757d; color: #fff; }
        .badge-primary { background: #0d6efd; color: #fff; }
        .badge-info { background: #0dcaf0; color: #333; }
        .badge-success { background: #198754; color: #fff; }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #e0e0e0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN TIKET MAINTENANCE</h1>
        <p>
            Periode: {{ request('start_date', 'Awal') }} s/d {{ request('end_date', 'Akhir') }}
            | Teknisi: {{ request('technician') && request('technician') !== 'all' ? \App\Models\User::find(request('technician'))?->name ?? 'Semua' : 'Semua' }}
            | Dicetak: {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>

    <div class="summary">
        <div class="summary-item bg-primary">
            <span class="number">{{ $totalTickets }}</span>
            Total Tiket
        </div>
        <div class="summary-item bg-danger">
            <span class="number">{{ $totalOpen }}</span>
            Open
        </div>
        <div class="summary-item bg-warning">
            <span class="number">{{ $tickets->where('status', 'in_progress')->count() }}</span>
            In Progress
        </div>
        <div class="summary-item bg-success">
            <span class="number">{{ $totalResolved }}</span>
            Selesai
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Tiket</th>
                <th>Mesin</th>
                <th>Mekanik</th>
                <th>Prioritas</th>
                <th>Status</th>
                <th>Target SLA</th>
                <th>Mulai</th>
                <th>Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
            <tr>
                <td>{{ $ticket->ticket_number }}</td>
                <td>{{ $ticket->machine?->fa_desc ?? 'N/A' }}</td>
                <td>{{ $ticket->assignedMechanic?->name ?? '-' }}</td>
                <td>
                    @if($ticket->priority == 'high')
                        <span class="badge badge-danger">High</span>
                    @elseif($ticket->priority == 'medium')
                        <span class="badge badge-warning">Medium</span>
                    @else
                        <span class="badge badge-secondary">Low</span>
                    @endif
                </td>
                <td>
                    @if($ticket->status == 'open')
                        <span class="badge badge-primary">Open</span>
                    @elseif($ticket->status == 'in_progress')
                        <span class="badge badge-info">In Progress</span>
                    @elseif($ticket->status == 'resolved')
                        <span class="badge badge-success">Resolved</span>
                    @else
                        <span class="badge badge-secondary">Closed</span>
                    @endif
                </td>
                <td>{{ $ticket->sla_target_hours ? $ticket->sla_target_hours . ' jam' : '-' }}</td>
                <td>{{ $ticket->started_at ? $ticket->started_at->format('d/m/Y H:i') : '-' }}</td>
                <td>{{ $ticket->resolved_at ? $ticket->resolved_at->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 30px; color: #999;">
                    Tidak ada data tiket untuk periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>MMS - Maintenance Management System &bull; Dicetak {{ now()->format('d F Y H:i') }}</p>
    </div>
</body>
</html>
