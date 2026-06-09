<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Mesin</title>
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
            border-bottom: 2px solid #e64980;
        }
        .header h1 {
            font-size: 18px;
            color: #e64980;
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
        .bg-warning { background: #ffc107; color: #333; }
        .bg-info { background: #0dcaf0; color: #333; }
        .bg-danger { background: #dc3545; }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        th {
            background: #e64980;
            color: #fff;
            padding: 6px 3px;
            text-align: left;
            font-weight: 600;
        }
        th.center { text-align: center; }
        td {
            padding: 4px 3px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: top;
        }
        td.center { text-align: center; }
        td.right { text-align: right; }
        tr:nth-child(even) { background: #f8f9fa; }
        .condition-good { background-color: #f0fff4 !important; }
        .condition-needs-repair { background-color: #fff8e1 !important; }
        .condition-repairing { background-color: #e3f2fd !important; }
        .condition-broken { background-color: #fff3f3 !important; }
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }
        .badge-success { background: #198754; color: #fff; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-info { background: #0dcaf0; color: #333; }
        .badge-danger { background: #dc3545; color: #fff; }
        .badge-secondary { background: #6c757d; color: #fff; }
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
        <h1>LAPORAN DATA MESIN</h1>
        <p>
            Total: {{ $totalMachines }} mesin
            | Dicetak: {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>

    <div class="summary">
        <div class="summary-item bg-primary">
            <span class="number">{{ $totalMachines }}</span>
            Total Mesin
        </div>
        <div class="summary-item bg-success">
            <span class="number">{{ $goodCondition }}</span>
            Good
        </div>
        <div class="summary-item bg-warning">
            <span class="number">{{ $needsRepair }}</span>
            Needs Repair
        </div>
        <div class="summary-item bg-info">
            <span class="number">{{ $repairing }}</span>
            Repairing
        </div>
        <div class="summary-item bg-danger">
            <span class="number">{{ $broken }}</span>
            Broken
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>FA Tag No</th>
                <th>Deskripsi Mesin</th>
                <th>Serial Number</th>
                <th>Section</th>
                <th>Lokasi</th>
                <th>Line</th>
                <th class="center">Kondisi</th>
                <th>Supplier</th>
                <th class="right">Nilai</th>
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
                <td>{{ $index + 1 }}</td>
                <td>{{ $machine->fa_tag_no }}</td>
                <td>{{ $machine->fa_desc }}</td>
                <td>{{ $machine->serial_number }}</td>
                <td>{{ $machine->sect_code }}</td>
                <td>{{ $machine->loc_code }}</td>
                <td>{{ $machine->line_code }}</td>
                <td class="center">
                    @if($machine->condition_status == 'Good')
                        <span class="badge badge-success">Good</span>
                    @elseif($machine->condition_status == 'Needs Repair')
                        <span class="badge badge-warning">Needs Repair</span>
                    @elseif($machine->condition_status == 'Repairing')
                        <span class="badge badge-info">Repairing</span>
                    @elseif($machine->condition_status == 'Broken')
                        <span class="badge badge-danger">Broken</span>
                    @else
                        <span class="badge badge-secondary">{{ $machine->condition_status ?? '-' }}</span>
                    @endif
                </td>
                <td>{{ $machine->supp_name }}</td>
                <td class="right">
                    @if($machine->acq_cost)
                        Rp {{ number_format((float) str_replace(['.', ','], ['', '.'], $machine->acq_cost), 0, ',', '.') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align: center; padding: 30px; color: #999;">
                    Tidak ada data mesin.
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